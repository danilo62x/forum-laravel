import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import ApexCharts from 'apexcharts';
import i18n, {
  LANGUAGES,
  formatDate,
  formatDateTime,
  formatTime,
  formatNumber,
} from './i18n.js';
import './pwa.js';

window.Alpine = Alpine;

// ─── Magics do Alpine expostos como globais ─────────────────────────────────
// As fábricas de página (homePage(), topicPage(), …) são funções declaradas num
// <script>, fora do escopo de expressão do Alpine — que é onde o with(scope)
// coloca os magics. O Alpine injeta os magics no OBJETO DE DADOS
// (injectMagics), não no window, então um `$store` ou `$t` nu dentro dessas
// funções seria ReferenceError. Espelhamos os magics em window para que elas
// continuem legíveis. Alpine.store() devolve o mesmo proxy reativo do magic,
// então os getters que leem daqui continuam reativos.
window.$store = new Proxy(
  {},
  { get: (_target, name) => (typeof name === 'string' ? Alpine.store(name) : undefined) }
);
const registerMagic = Alpine.magic.bind(Alpine);
Alpine.magic = (name, callback) => {
  registerMagic(name, callback);
  Object.defineProperty(window, `$${name}`, { configurable: true, get: () => callback() });
};

Alpine.plugin(collapse);
window.ApexCharts = ApexCharts;

// ─── localStorage helpers (mirror zustand persist) ──────────────────────────
function load(key, fallback) {
  try {
    const raw = localStorage.getItem(key);
    if (!raw) return fallback;
    const parsed = JSON.parse(raw);
    // zustand persist wraps state in { state, version }
    return parsed && parsed.state ? parsed.state : parsed;
  } catch (_) {
    return fallback;
  }
}
function save(key, state) {
  try {
    localStorage.setItem(key, JSON.stringify({ state, version: 0 }));
  } catch (_) {}
}

const FO = window.FO || {};
const clone = (value) => JSON.parse(JSON.stringify(value ?? null));

// ════════════════════════════════════════════════════════════════════════════
// Helpers — porte de forum/react src/data/query.ts + src/lib/format.ts
// ════════════════════════════════════════════════════════════════════════════

// ---- Imagens (mirror mock.ts foImage) ----
const IMG = {
  work: [
    '1497366216548-37526070297c', '1497366811353-6870744d04b2', '1542744173-8e7e53415bb0',
    '1568992687947-868a62a9f521', '1521737711867-e3b97375f902', '1504384308090-c894fdcc538d',
    '1560264280-88b68371db39', '1486312338219-ce68d2c6f44d', '1521791136064-7986c2920216',
    '1560472354-b33ff0c44a43', '1516321318423-f06f85e504b3', '1600880292203-757bb62b4baf',
  ],
  city: ['1477959858617-67f85cf4f1df', '1496568816309-51d7c20e3b21', '1480714378408-67cf0d13bc1b'],
  people: [
    '1500648767791-00dcc994a43e', '1494790108377-be9c29b29330', '1507003211169-0a1dd7228f2d',
    '1438761681033-6461ffad8d80', '1633332755192-727a05c4013d', '1573496359142-b8d87734a5a2',
  ],
};
function imgHash(s) {
  let h = 0;
  for (let i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) >>> 0;
  return h;
}
function imgCategory(seed) {
  const s = (seed || '').toLowerCase();
  if (/author|avatar|member|membro|people|person|profile|staff|team|user/.test(s)) return 'people';
  if (/city|encontro|hero|urban|cidade/.test(s)) return 'city';
  if (/capa|categoria|cover/.test(s)) return imgHash(s) % 3 === 0 ? 'city' : 'work';
  return 'work';
}
function unsplash(id, w, h) {
  return `https://images.unsplash.com/photo-${id}?w=${w}&h=${h}&fit=crop&q=70&auto=format`;
}
/** URL determinística por seed — nunca picsum. */
function foImage(seed, w = 960, h = 640) {
  const ids = IMG[imgCategory(seed)];
  return unsplash(ids[imgHash('fo-' + seed) % ids.length], w, h);
}

// ---- Lookups ----
const categoryById = (list, id) => (list || []).find((c) => c.id === id);
const categoryBySlug = (list, slug) => (list || []).find((c) => c.slug === slug);
const topicById = (list, id) => (list || []).find((t) => t.id === id);
const topicBySlug = (list, slug) => (list || []).find((t) => t.slug === slug);
const memberById = (list, id) => (list || []).find((m) => m.id === id);
const memberBySlug = (list, slug) => (list || []).find((m) => m.slug === slug);
const replyById = (list, id) => (list || []).find((r) => r.id === id);

/** Slug simples, usado ao criar tópico e categoria. */
function slugify(value) {
  return (value || '')
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');
}

// ---- Métricas derivadas ----
/** Score = votos a favor menos votos contra. Nunca é um campo gravado. */
const score = (entity) => entity.upvotes - entity.downvotes;

const HOUR_MS = 60 * 60 * 1000;
const DAY_MS = 24 * HOUR_MS;

const hoursSince = (iso, now = Date.now()) => Math.max(0, (now - new Date(iso).getTime()) / HOUR_MS);
const daysSince = (iso, now = Date.now()) => Math.max(0, (now - new Date(iso).getTime()) / DAY_MS);

/**
 * "Em alta": `(score * 2 + replyCount) / horas desde a última resposta`.
 * O piso de uma hora evita explodir o valor de quem acabou de responder.
 */
function trendingScore(topic, now = Date.now()) {
  const hours = Math.max(1, hoursSince(topic.lastReplyAt, now));
  return (score(topic) * 2 + topic.replyCount) / hours;
}

/** Um membro só está silenciado enquanto o prazo não expira. */
function isMuted(member, now = Date.now()) {
  if (!member || !member.mutedUntil) return false;
  return new Date(member.mutedUntil).getTime() > now;
}

/** Pode escrever no fórum: existe, não está banido e não está silenciado. */
const canPost = (member, now = Date.now()) => Boolean(member) && !member.banned && !isMuted(member, now);

/** Dias que ainda faltam do silêncio (mínimo 1) — usado nas pílulas. */
const mutedDays = (until) => Math.max(1, Math.ceil((new Date(until).getTime() - Date.now()) / 86400000));

// ---- Listas do fórum público ----
const visibleTopics = (list) => (list || []).filter((t) => t.status !== 'oculto');
const topicsOfCategory = (list, categoryId) => visibleTopics(list).filter((t) => t.categoryId === categoryId);
const topicsOfAuthor = (list, authorId) => visibleTopics(list).filter((t) => t.authorId === authorId);

const pinnedFirst = (a, b) => Number(b.status === 'fixado') - Number(a.status === 'fixado');

function sortTopics(list, sort, now = Date.now()) {
  const rows = [...list];
  switch (sort) {
    case 'votados':
      return rows.sort((a, b) => pinnedFirst(a, b) || score(b) - score(a) || b.views - a.views);
    case 'sem-resposta':
      return rows
        .filter((t) => t.replyCount === 0)
        .sort((a, b) => pinnedFirst(a, b) || b.at.localeCompare(a.at));
    case 'em-alta':
      return rows.sort((a, b) => pinnedFirst(a, b) || trendingScore(b, now) - trendingScore(a, now));
    default:
      return rows.sort((a, b) => pinnedFirst(a, b) || b.lastReplyAt.localeCompare(a.lastReplyAt));
  }
}

/** Em alta na home — sem privilegiar tópico fixado. */
function trendingTopics(list, limit = 5, now = Date.now()) {
  return visibleTopics(list)
    .filter((t) => t.status !== 'fixado')
    .sort((a, b) => trendingScore(b, now) - trendingScore(a, now))
    .slice(0, limit);
}

const latestTopics = (list, limit = 6) =>
  visibleTopics(list)
    .sort((a, b) => pinnedFirst(a, b) || b.lastReplyAt.localeCompare(a.lastReplyAt))
    .slice(0, limit);

function unansweredTopics(list, limit) {
  const rows = visibleTopics(list)
    .filter((t) => t.replyCount === 0 && t.status !== 'fechado')
    .sort((a, b) => b.at.localeCompare(a.at));
  return limit ? rows.slice(0, limit) : rows;
}

/** Tópicos parecidos: mesma categoria vale mais, tags em comum somam. */
function relatedTopics(list, current, limit = 4) {
  const others = visibleTopics(list).filter((t) => t.id !== current.id);
  const weight = (t) => {
    let s = 0;
    if (t.categoryId === current.categoryId) s += 3;
    s += t.tags.filter((tag) => current.tags.includes(tag)).length * 2;
    return s;
  };
  return others
    .map((t) => ({ t, s: weight(t) }))
    .filter((row) => row.s > 0)
    .sort((a, b) => b.s - a.s || score(b.t) - score(a.t))
    .slice(0, limit)
    .map((row) => row.t);
}

function allTags(list) {
  const seen = [];
  for (const topic of list || []) for (const tag of topic.tags) if (!seen.includes(tag)) seen.push(tag);
  return seen.sort((a, b) => a.localeCompare(b));
}

// ---- Filtros ----
const topicText = (topic) => [topic.title, topic.body, topic.tags.join(' ')].join(' ');

function filterTopics(list, f = {}) {
  const term = (f.search ?? '').trim().toLowerCase();
  return (list || []).filter((t) => {
    if (f.publicOnly && t.status === 'oculto') return false;
    if (f.categoryId && f.categoryId !== 'all' && t.categoryId !== Number(f.categoryId)) return false;
    if (f.status && f.status !== 'all' && t.status !== f.status) return false;
    if (f.authorId && f.authorId !== 'all' && t.authorId !== Number(f.authorId)) return false;
    if (f.tag && f.tag !== 'all' && !t.tags.includes(f.tag)) return false;
    if (f.onlyUnanswered && t.replyCount > 0) return false;
    if (!term) return true;
    return topicText(t).toLowerCase().includes(term);
  });
}

// ---- Respostas ----
const repliesOfTopic = (list, topicId, includeHidden = false) =>
  (list || [])
    .filter((r) => r.topicId === topicId && (includeHidden || !r.hidden))
    .sort((a, b) => a.at.localeCompare(b.at));

/**
 * Árvore de dois níveis. A melhor resposta sobe para o topo levando junto a
 * sub-thread dela; o resto segue a ordem cronológica.
 */
function buildReplyTree(list, topicId, includeHidden = false) {
  const rows = repliesOfTopic(list, topicId, includeHidden);
  const roots = rows.filter((r) => r.parentId === null);
  const nodes = roots.map((reply) => ({ reply, children: rows.filter((r) => r.parentId === reply.id) }));
  return nodes.sort((a, b) => Number(b.reply.best) - Number(a.reply.best));
}

/** Respostas do dia — usado no KPI da moderação. */
function repliesToday(list, now = new Date()) {
  const start = new Date(now);
  start.setHours(0, 0, 0, 0);
  return (list || []).filter((r) => new Date(r.at).getTime() >= start.getTime());
}

const repliesOfAuthor = (list, authorId, includeHidden = false) =>
  (list || [])
    .filter((r) => r.authorId === authorId && (includeHidden || !r.hidden))
    .sort((a, b) => b.at.localeCompare(a.at));

// ---- Busca ----
/** Trecho com o termo isolado no meio, para destacar no resultado. */
function makeSnippet(text, term, radius = 90) {
  const clean = (text || '').replace(/\s+/g, ' ').trim();
  const needle = (term || '').trim().toLowerCase();
  if (!needle) return { before: clean.slice(0, radius * 2), match: '', after: '' };
  const at = clean.toLowerCase().indexOf(needle);
  if (at === -1) return { before: clean.slice(0, radius * 2), match: '', after: '' };
  const start = Math.max(0, at - radius);
  const end = Math.min(clean.length, at + needle.length + radius);
  return {
    before: (start > 0 ? '… ' : '') + clean.slice(start, at),
    match: clean.slice(at, at + needle.length),
    after: clean.slice(at + needle.length, end) + (end < clean.length ? ' …' : ''),
  };
}

/**
 * Busca do fórum: título vale mais que corpo, e uma resposta que casa devolve
 * o tópico com a âncora da resposta para a página rolar até ela.
 */
function searchForum(topics, replies, term, f = {}) {
  const needle = (term || '').trim().toLowerCase();
  const scoped = filterTopics(topics, { ...f, publicOnly: true, onlyUnanswered: f.onlyUnanswered });

  if (!needle) {
    return scoped
      .sort((a, b) => b.lastReplyAt.localeCompare(a.lastReplyAt))
      .map((topic) => ({ topic, reply: null, snippet: makeSnippet(topic.body, ''), score: 0 }));
  }

  return scoped
    .map((topic) => {
      const title = topic.title.toLowerCase();
      const body = topic.body.toLowerCase();
      const hitReply = repliesOfTopic(replies, topic.id).find((r) => r.body.toLowerCase().includes(needle));

      let hitScore = 0;
      if (title.includes(needle)) hitScore += 10;
      if (topic.tags.some((tag) => tag.toLowerCase().includes(needle))) hitScore += 4;
      if (body.includes(needle)) hitScore += 3;
      if (hitReply) hitScore += 2;

      const source = body.includes(needle) ? topic.body : hitReply ? hitReply.body : topic.body;

      return {
        topic,
        reply: body.includes(needle) ? null : hitReply ?? null,
        snippet: makeSnippet(source, needle),
        score: hitScore,
      };
    })
    .filter((hit) => hit.score > 0)
    .sort((a, b) => b.score - a.score || b.topic.views - a.topic.views);
}

// ---- Reputação e distintivos ----
/** Limiares dos distintivos — derivados, nunca gravados. */
const BADGE_RULES = {
  equipe: 'equipe',
  veterano: 'veterano',
  primeiroTopico: 'primeiro-topico',
  dezRespostas: 'dez-respostas',
  cinquentaRespostas: 'cinquenta-respostas',
  melhorResposta: 'melhor-resposta',
  cemReputacao: 'cem-reputacao',
  quinhentosReputacao: 'quinhentos-reputacao',
};

const BADGE_ICONS = {
  equipe: 'shield-check',
  veterano: 'clock',
  'primeiro-topico': 'message-square',
  'dez-respostas': 'message-circle',
  'cinquenta-respostas': 'layers',
  'melhor-resposta': 'check-circle',
  'cem-reputacao': 'star',
  'quinhentos-reputacao': 'award',
};
const badgeIcon = (name) => BADGE_ICONS[name] ?? 'award';

/**
 * Reputação derivada do que a pessoa produziu: votos recebidos pesam mais que
 * volume, e melhor resposta é o que mais conta.
 */
function memberActivity(member, topics, replies) {
  const own = (topics || []).filter((t) => t.authorId === member.id && t.status !== 'oculto');
  const answers = (replies || []).filter((r) => r.authorId === member.id && !r.hidden);
  const bestCount = answers.filter((r) => r.best).length;
  const topicScore = own.reduce((sum, t) => sum + score(t), 0);
  const replyScore = answers.reduce((sum, r) => sum + score(r), 0);
  const reputation = Math.max(
    0,
    Math.round(topicScore + replyScore + bestCount * 15 + own.length * 2 + answers.length)
  );
  return { topicCount: own.length, replyCount: answers.length, bestCount, reputation };
}

function badgesFor(member, activity, now = Date.now()) {
  const out = [];
  if (member.role !== 'membro') out.push(BADGE_RULES.equipe);
  if (daysSince(member.joinedAt, now) >= 365) out.push(BADGE_RULES.veterano);
  if (activity.topicCount >= 1) out.push(BADGE_RULES.primeiroTopico);
  if (activity.replyCount >= 10) out.push(BADGE_RULES.dezRespostas);
  if (activity.replyCount >= 50) out.push(BADGE_RULES.cinquentaRespostas);
  if (activity.bestCount >= 1) out.push(BADGE_RULES.melhorResposta);
  if (activity.reputation >= 100) out.push(BADGE_RULES.cemReputacao);
  if (activity.reputation >= 500) out.push(BADGE_RULES.quinhentosReputacao);
  return out;
}

const topMembers = (list, limit = 6) =>
  [...(list || [])].filter((m) => !m.banned).sort((a, b) => b.reputation - a.reputation).slice(0, limit);

const newMembers = (list, days = 30, now = Date.now()) =>
  (list || []).filter((m) => daysSince(m.joinedAt, now) <= days);

// ---- Séries para os gráficos ----
const dayLabel = (d) => `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}`;

/** Tópicos + respostas por dia nos últimos N dias (moderação). */
function activityByDay(topics, replies, days = 14, now = new Date()) {
  const topicSeries = [];
  const replySeries = [];
  for (let i = days - 1; i >= 0; i--) {
    const start = new Date(now);
    start.setHours(0, 0, 0, 0);
    start.setDate(start.getDate() - i);
    const end = new Date(start);
    end.setDate(end.getDate() + 1);
    const inRange = (iso) => {
      const ts = new Date(iso).getTime();
      return ts >= start.getTime() && ts < end.getTime();
    };
    const label = dayLabel(start);
    topicSeries.push({ label, value: (topics || []).filter((t) => inRange(t.at)).length });
    replySeries.push({ label, value: (replies || []).filter((r) => inRange(r.at)).length });
  }
  return { topics: topicSeries, replies: replySeries };
}

/** Atividade do membro por mês (perfil público). */
function activityByMonth(member, topics, replies, months = 6, now = new Date()) {
  const out = [];
  for (let i = months - 1; i >= 0; i--) {
    const start = new Date(now.getFullYear(), now.getMonth() - i, 1);
    const end = new Date(now.getFullYear(), now.getMonth() - i + 1, 1);
    const inRange = (iso) => {
      const ts = new Date(iso).getTime();
      return ts >= start.getTime() && ts < end.getTime();
    };
    const value =
      (topics || []).filter((t) => t.authorId === member.id && inRange(t.at)).length +
      (replies || []).filter((r) => r.authorId === member.id && inRange(r.at)).length;
    out.push({
      label: `${String(start.getMonth() + 1).padStart(2, '0')}/${String(start.getFullYear()).slice(2)}`,
      value,
    });
  }
  return out;
}

// ---- Denúncias ----
function reportRows(reports, topics, replies, members) {
  return [...(reports || [])]
    .sort((a, b) => Number(a.resolved) - Number(b.resolved) || b.at.localeCompare(a.at))
    .map((report) => {
      const reply = report.targetType === 'resposta' ? replyById(replies, report.targetId) : undefined;
      const topic =
        report.targetType === 'topico'
          ? topicById(topics, report.targetId)
          : reply
            ? topicById(topics, reply.topicId)
            : undefined;
      return { report, topic, reply, reporter: memberById(members, report.reporterId) };
    });
}

const openReports = (reports) => (reports || []).filter((r) => !r.resolved);

function reportsByReason(reports) {
  const reasons = ['spam', 'ofensa', 'fora-do-tema', 'duplicado'];
  return reasons.map((reason) => ({
    reason,
    value: (reports || []).filter((r) => !r.resolved && r.reason === reason).length,
  }));
}

// ---- Paginação ----
const paginate = (list, page, perPage) => list.slice((page - 1) * perPage, (page - 1) * perPage + perPage);
const pageCount = (total, perPage) => Math.max(1, Math.ceil(total / perPage));

/** Números de página com reticências, igual a ui/Pagination.tsx. */
function pageNumbers(page, totalPages) {
  const pages = [];
  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || (i >= page - 1 && i <= page + 1)) pages.push(i);
    else if (pages[pages.length - 1] !== '...') pages.push('...');
  }
  return pages;
}

// ---- Tempo relativo (mirror lib/format.ts relativeParts) ----
function relativeParts(iso, now = Date.now()) {
  const diff = Math.max(0, now - new Date(iso).getTime());
  const minutes = Math.floor(diff / 60000);
  if (minutes < 1) return { unit: 'now', count: 0 };
  if (minutes < 60) return { unit: 'minutes', count: minutes };
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return { unit: 'hours', count: hours };
  const days = Math.floor(hours / 24);
  if (days < 30) return { unit: 'days', count: days };
  const months = Math.floor(days / 30);
  if (months < 12) return { unit: 'months', count: months };
  return { unit: 'years', count: Math.floor(months / 12) };
}

// ---- Pílulas (mirror fo/StatusPill.tsx + ui/Badge.tsx) ----
const BADGE_SOFT = {
  brand: 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
  success: 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
  warning: 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500',
  error: 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
  gray: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
};
const BADGE_SOLID = {
  brand: 'bg-brand-500 text-white',
  success: 'bg-success-500 text-white',
  warning: 'bg-warning-500 text-white',
  error: 'bg-error-500 text-white',
  gray: 'bg-gray-500 text-white',
};
const STATUS_COLOR = { aberto: 'success', fechado: 'gray', fixado: 'brand', oculto: 'error' };
const STATUS_ICON = { aberto: 'message-circle', fechado: 'lock', fixado: 'pin', oculto: 'eye-off' };
const ROLE_COLOR = { membro: 'gray', moderador: 'brand', admin: 'warning' };
const REASON_COLOR = { spam: 'error', ofensa: 'warning', 'fora-do-tema': 'gray', duplicado: 'brand' };

const badgeClass = (color = 'brand', variant = 'soft') =>
  (variant === 'solid' ? BADGE_SOLID : BADGE_SOFT)[color] ?? BADGE_SOFT.brand;
const statusColor = (status) => STATUS_COLOR[status] ?? 'gray';
const statusIcon = (status) => STATUS_ICON[status] ?? 'message-circle';
const roleColor = (role) => ROLE_COLOR[role] ?? 'gray';
const reasonColor = (reason) => REASON_COLOR[reason] ?? 'gray';

/** `**negrito**` e `código` entre crases viram HTML — mirror fo/RichBody.tsx. */
const escapeHtml = (s) =>
  String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

function inlineHtml(text) {
  return (text || '')
    .split(/(\*\*[^*]+\*\*|`[^`]+`)/g)
    .filter(Boolean)
    .map((part) => {
      if (part.startsWith('**') && part.endsWith('**')) {
        return `<strong class="font-semibold text-heading">${escapeHtml(part.slice(2, -2))}</strong>`;
      }
      if (part.startsWith('`') && part.endsWith('`')) {
        return `<code class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[0.85em] text-brand-600 dark:bg-gray-800 dark:text-brand-400">${escapeHtml(
          part.slice(1, -1)
        )}</code>`;
      }
      return escapeHtml(part);
    })
    .join('');
}

/**
 * Corpo de tópico e de resposta. O texto é guardado como uma string simples e
 * ganha estrutura aqui: título de seção, lista, citação e bloco de código.
 */
function richBody(text, clamp) {
  if (clamp) {
    const flat = (text || '').replace(/[#>`*-]/g, '').replace(/\s+/g, ' ').trim();
    const short = flat.length > clamp ? `${flat.slice(0, clamp).trimEnd()}…` : flat;
    return `<p class="text-sm text-muted">${escapeHtml(short)}</p>`;
  }

  const lines = (text || '').split('\n');
  const out = [];
  let list = [];
  let code = null;

  const flushList = () => {
    if (!list.length) return;
    out.push(
      `<ul class="my-3 list-disc space-y-1.5 pl-5 text-sm leading-relaxed text-body">${list
        .map((item) => `<li>${inlineHtml(item)}</li>`)
        .join('')}</ul>`
    );
    list = [];
  };

  lines.forEach((raw) => {
    const line = raw.replace(/\s+$/, '');

    if (line.trim().startsWith('```')) {
      if (code) {
        out.push(
          `<pre class="my-3 overflow-x-auto rounded-lg bg-gray-900 p-4 text-xs leading-relaxed text-gray-100"><code>${escapeHtml(
            code.join('\n')
          )}</code></pre>`
        );
        code = null;
      } else {
        flushList();
        code = [];
      }
      return;
    }
    if (code) {
      code.push(raw);
      return;
    }

    if (line.startsWith('- ')) {
      list.push(line.slice(2));
      return;
    }
    flushList();

    if (!line.trim()) return;

    if (line.startsWith('### ')) {
      out.push(
        `<h3 class="mt-5 mb-2 text-base font-semibold text-heading first:mt-0">${inlineHtml(line.slice(4))}</h3>`
      );
      return;
    }
    if (line.startsWith('> ')) {
      out.push(
        `<blockquote class="my-3 border-l-4 border-brand-200 pl-4 text-sm text-muted italic dark:border-brand-500/40">${inlineHtml(
          line.slice(2)
        )}</blockquote>`
      );
      return;
    }
    out.push(`<p class="my-3 text-sm leading-relaxed text-body first:mt-0">${inlineHtml(line)}</p>`);
  });

  flushList();
  return out.join('');
}

// ════════════════════════════════════════════════════════════════════════════
// Magics de i18n / formatação
// ════════════════════════════════════════════════════════════════════════════
// Tocar o `ui.lang` reativo dentro de cada magic faz x-text/x-bind reexecutarem
// quando o idioma muda (mantém a UI inteira reativa à troca de idioma).
Alpine.magic('t', () => (key, params) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return i18n.t(key, params);
});
Alpine.magic('date', () => (iso) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return formatDate(iso, i18n.lang);
});
Alpine.magic('datetime', () => (iso) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return formatDateTime(iso, i18n.lang);
});
Alpine.magic('time', () => (iso) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return formatTime(iso, i18n.lang);
});
Alpine.magic('number', () => (value) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return formatNumber(value ?? 0, i18n.lang);
});
Alpine.magic('lang', () => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return i18n.lang;
});
/** Tempo relativo já traduzido: `$rel(iso)` → "há 3 dias". */
Alpine.magic('rel', () => (iso) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  const parts = relativeParts(iso);
  return i18n.t(`time.${parts.unit}`, { count: parts.count });
});
/** Chama qualquer fn de window.foHelpers de forma reativa ao idioma. */
Alpine.magic('foH', () => (name, ...args) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  const fn = window.foHelpers && window.foHelpers[name];
  return typeof fn === 'function' ? fn(...args) : undefined;
});

// ════════════════════════════════════════════════════════════════════════════
// Stores
// ════════════════════════════════════════════════════════════════════════════

// ─── UI store — chave fo-ui (tema, sidebar, toasts, cookies, sub-threads) ───
let toastId = 0;
const savedUi = load('fo-ui', {});
Alpine.store('ui', {
  darkMode: savedUi.darkMode ?? false,
  sidebarCollapsed: savedUi.sidebarCollapsed ?? false,
  sidebarMobileOpen: false,
  toasts: [],
  cookiesAccepted: savedUi.cookiesAccepted ?? false,
  showCookieBanner: false,
  /** Ids das respostas de primeiro nível com a sub-thread recolhida. */
  collapsedThreads: savedUi.collapsedThreads ?? [],
  lang: i18n.lang,

  init() {
    this.applyTheme();
    Alpine.effect(() => {
      this.darkMode;
      this.sidebarCollapsed;
      this.cookiesAccepted;
      this.collapsedThreads.length;
      this.applyTheme();
      save('fo-ui', {
        darkMode: this.darkMode,
        sidebarCollapsed: this.sidebarCollapsed,
        cookiesAccepted: this.cookiesAccepted,
        collapsedThreads: this.collapsedThreads,
      });
    });
    document.addEventListener('i18n:changed', (e) => {
      this.lang = e.detail.lang;
    });
    if (!this.cookiesAccepted) {
      setTimeout(() => this.openCookieBanner(), 800);
    }
  },
  applyTheme() {
    const root = document.documentElement;
    if (this.darkMode) root.classList.add('dark');
    else root.classList.remove('dark');
  },
  toggleDarkMode() {
    this.darkMode = !this.darkMode;
  },
  setDarkMode(val) {
    this.darkMode = val;
  },
  toggleSidebar() {
    this.sidebarCollapsed = !this.sidebarCollapsed;
  },
  toggleSidebarMobile(val) {
    this.sidebarMobileOpen = val !== undefined ? val : !this.sidebarMobileOpen;
  },
  setLang(lang) {
    i18n.setLang(lang);
    this.lang = lang;
  },
  get languages() {
    return LANGUAGES;
  },
  get currentLanguage() {
    return LANGUAGES.find((l) => l.code === this.lang) || LANGUAGES[0];
  },

  notify(message, type = 'info', duration = 4000) {
    const id = ++toastId;
    this.toasts.push({ id, message, type, duration });
    setTimeout(() => this.dismissToast(id), duration);
  },
  notifySuccess(m) {
    this.notify(m, 'success');
  },
  notifyError(m) {
    this.notify(m, 'error');
  },
  notifyWarning(m) {
    this.notify(m, 'warning');
  },
  dismissToast(id) {
    const i = this.toasts.findIndex((t) => t.id === id);
    if (i !== -1) this.toasts.splice(i, 1);
  },
  acceptCookies() {
    this.cookiesAccepted = true;
    this.showCookieBanner = false;
  },
  declineCookies() {
    this.cookiesAccepted = false;
    this.showCookieBanner = false;
  },
  openCookieBanner() {
    this.showCookieBanner = true;
  },
  closeCookieBanner() {
    this.showCookieBanner = false;
  },

  // ── Sub-threads recolhidas ──
  toggleThread(replyId) {
    const i = this.collapsedThreads.indexOf(replyId);
    if (i === -1) this.collapsedThreads.push(replyId);
    else this.collapsedThreads.splice(i, 1);
  },
  expandThread(replyId) {
    const i = this.collapsedThreads.indexOf(replyId);
    if (i !== -1) this.collapsedThreads.splice(i, 1);
  },
  isThreadCollapsed(replyId) {
    return this.collapsedThreads.includes(replyId);
  },
});

// ─── Auth store — chave fo-auth (demo moderador@agora.com / demo123) ────────
Alpine.store('auth', {
  user: load('fo-auth', { user: null }).user ?? null,

  init() {
    // A sessão do Laravel manda: quem está logado (ou não) vem do servidor.
    this.user = FO.user ?? null;
    Alpine.effect(() => {
      this.user;
      save('fo-auth', { user: this.user });
    });
  },
  get isAuthenticated() {
    return this.user !== null;
  },
  setUser(user) {
    this.user = user;
  },
  logout() {
    this.user = null;
  },
  updateProfile(patch) {
    if (!this.user) return;
    this.user = { ...this.user, ...patch };
  },
});

// ─── Composer store — chave fo-composer (um rascunho por tópico) ────────────
const savedComposer = load('fo-composer', {});
const EMPTY_DRAFT = { body: '', parentId: null, at: '' };
Alpine.store('composer', {
  drafts: savedComposer.drafts ?? {},

  init() {
    Alpine.effect(() => {
      Object.keys(this.drafts).length;
      save('fo-composer', { drafts: this.drafts });
    });
  },
  persist() {
    save('fo-composer', { drafts: this.drafts });
  },
  draftOf(topicId) {
    return this.drafts[topicId] ?? EMPTY_DRAFT;
  },
  setBody(topicId, body) {
    const current = this.draftOf(topicId);
    this.drafts = { ...this.drafts, [topicId]: { ...current, body, at: new Date().toISOString() } };
    this.persist();
  },
  setParent(topicId, parentId) {
    const current = this.draftOf(topicId);
    this.drafts = { ...this.drafts, [topicId]: { ...current, parentId, at: new Date().toISOString() } };
    this.persist();
  },
  clearDraft(topicId) {
    const next = { ...this.drafts };
    delete next[topicId];
    this.drafts = next;
    this.persist();
  },
  hasDraft(topicId) {
    return Boolean(this.drafts[topicId] && this.drafts[topicId].body.trim());
  },
  draftCount() {
    return Object.values(this.drafts).filter((d) => d.body.trim()).length;
  },
});

// ─── Forum store — chave fo-data ────────────────────────────────────────────
// Espelha forum/react stores/forum.ts: categorias / tópicos / respostas /
// membros / denúncias, com as mesmas ações e a mesma derivação.
const nextId = (rows) => (rows.length ? Math.max(...rows.map((r) => r.id)) + 1 : 1);

/**
 * Recalcula tudo que é métrica: contadores de categoria, resumo do tópico e
 * reputação/distintivos do membro. Nada disso é editado à mão em lugar nenhum.
 */
function derive(data) {
  const topics = data.topics.map((topic) => {
    const visible = data.replies.filter((r) => r.topicId === topic.id && !r.hidden);
    const best = visible.find((r) => r.best);
    const lastReplyAt = visible.reduce((acc, r) => (r.at > acc ? r.at : acc), topic.at);
    return {
      ...topic,
      replyCount: visible.length,
      lastReplyAt,
      bestReplyId: best ? best.id : null,
      solved: Boolean(best),
    };
  });

  const categories = data.categories
    .map((c) => ({
      ...c,
      topicCount: topics.filter((t) => t.categoryId === c.id && t.status !== 'oculto').length,
    }))
    .sort((a, b) => a.order - b.order);

  const members = data.members.map((m) => {
    const activity = memberActivity(m, topics, data.replies);
    return {
      ...m,
      topicCount: activity.topicCount,
      replyCount: activity.replyCount,
      reputation: activity.reputation,
      badges: badgesFor(m, activity),
    };
  });

  return { categories, topics, replies: data.replies, members, reports: data.reports };
}

const seed = () =>
  derive({
    categories: clone(FO.categories || []),
    topics: clone(FO.topics || []),
    replies: clone(FO.replies || []),
    members: clone(FO.members || []),
    reports: clone(FO.reports || []),
  });

const savedData = load('fo-data', {});
const initial = savedData.topics ? { ...savedData } : seed();

Alpine.store('forum', {
  categories: initial.categories ?? [],
  topics: initial.topics ?? [],
  replies: initial.replies ?? [],
  members: initial.members ?? [],
  reports: initial.reports ?? [],
  /** Membro que representa quem está navegando (conta demo). */
  currentMemberId: savedData.currentMemberId ?? 1,
  /** Voto do usuário por tópico e por resposta — tri-estado. */
  topicVotes: savedData.topicVotes ?? {},
  replyVotes: savedData.replyVotes ?? {},

  init() {
    // O que veio do armazenamento pode ter derivados velhos: recalcula.
    this.refresh();
  },

  persist() {
    save('fo-data', {
      categories: this.categories,
      topics: this.topics,
      replies: this.replies,
      members: this.members,
      reports: this.reports,
      topicVotes: this.topicVotes,
      replyVotes: this.replyVotes,
      currentMemberId: this.currentMemberId,
    });
  },

  /** Aplica a mudança e recalcula os derivados numa tacada só. */
  commit(patch) {
    const next = derive({
      categories: this.categories,
      topics: this.topics,
      replies: this.replies,
      members: this.members,
      reports: this.reports,
      ...patch,
    });
    this.categories = next.categories;
    this.topics = next.topics;
    this.replies = next.replies;
    this.members = next.members;
    this.reports = next.reports;
    this.persist();
  },

  refresh() {
    this.commit({});
  },

  // ---- Categorias ----
  addCategory(data) {
    const category = { ...data, id: nextId(this.categories), topicCount: 0 };
    this.commit({ categories: [...this.categories, category] });
    return category;
  },
  updateCategory(id, patch) {
    this.commit({ categories: this.categories.map((c) => (c.id === id ? { ...c, ...patch } : c)) });
  },
  removeCategory(id, moveToId) {
    const categories = this.categories.filter((c) => c.id !== id);
    const fallback = moveToId ?? (categories.length ? categories[0].id : null);
    const topics = fallback
      ? this.topics.map((t) => (t.categoryId === id ? { ...t, categoryId: fallback } : t))
      : this.topics.filter((t) => t.categoryId !== id);
    const keptIds = topics.map((t) => t.id);
    this.commit({
      categories,
      topics,
      replies: this.replies.filter((r) => keptIds.includes(r.topicId)),
    });
  },
  moveCategoryOrder(id, direction) {
    const rows = [...this.categories].sort((a, b) => a.order - b.order);
    const index = rows.findIndex((c) => c.id === id);
    const target = index + direction;
    if (index < 0 || target < 0 || target >= rows.length) return;
    const swapped = [...rows];
    const tmp = swapped[index];
    swapped[index] = swapped[target];
    swapped[target] = tmp;
    this.commit({ categories: swapped.map((c, i) => ({ ...c, order: i + 1 })) });
  },

  // ---- Tópicos ----
  addTopic(data) {
    const now = new Date().toISOString();
    const base = slugify(data.title);
    const id = nextId(this.topics);
    const topic = {
      id,
      slug: base ? `${base}-${id}` : `topico-${id}`,
      title: data.title,
      categoryId: data.categoryId,
      authorId: data.authorId,
      body: data.body,
      at: now,
      tags: data.tags,
      status: data.status ?? 'aberto',
      views: 0,
      upvotes: 0,
      downvotes: 0,
      replyCount: 0,
      lastReplyAt: now,
      bestReplyId: null,
      solved: false,
    };
    this.commit({ topics: [...this.topics, topic] });
    return topic;
  },
  updateTopic(id, patch) {
    this.commit({ topics: this.topics.map((t) => (t.id === id ? { ...t, ...patch } : t)) });
  },
  removeTopic(id) {
    this.commit({
      topics: this.topics.filter((t) => t.id !== id),
      replies: this.replies.filter((r) => r.topicId !== id),
      reports: this.reports.filter((r) => !(r.targetType === 'topico' && r.targetId === id)),
    });
  },
  setTopicStatus(id, status) {
    this.commit({ topics: this.topics.map((t) => (t.id === id ? { ...t, status } : t)) });
  },
  togglePinned(id) {
    this.commit({
      topics: this.topics.map((t) =>
        t.id === id ? { ...t, status: t.status === 'fixado' ? 'aberto' : 'fixado' } : t
      ),
    });
  },
  toggleClosed(id) {
    this.commit({
      topics: this.topics.map((t) =>
        t.id === id ? { ...t, status: t.status === 'fechado' ? 'aberto' : 'fechado' } : t
      ),
    });
  },
  moveTopic(id, categoryId) {
    this.commit({ topics: this.topics.map((t) => (t.id === id ? { ...t, categoryId } : t)) });
  },
  registerView(id) {
    this.commit({ topics: this.topics.map((t) => (t.id === id ? { ...t, views: t.views + 1 } : t)) });
  },

  // ---- Respostas ----
  addReply(topicId, authorId, body, parentId) {
    const text = (body || '').trim();
    if (!text) return null;
    // Limite de dois níveis: responder a uma sub-resposta cai no pai dela.
    let parent = parentId;
    if (parent !== null && parent !== undefined) {
      const target = this.replies.find((r) => r.id === parent);
      parent = target ? target.parentId ?? target.id : null;
    } else {
      parent = null;
    }
    const reply = {
      id: nextId(this.replies),
      topicId,
      parentId: parent,
      authorId,
      body: text,
      at: new Date().toISOString(),
      upvotes: 0,
      downvotes: 0,
      best: false,
      hidden: false,
      edited: false,
    };
    this.commit({ replies: [...this.replies, reply] });
    return reply;
  },
  updateReply(id, body) {
    this.commit({ replies: this.replies.map((r) => (r.id === id ? { ...r, body, edited: true } : r)) });
  },
  removeReply(id) {
    this.commit({
      replies: this.replies.filter((r) => r.id !== id && r.parentId !== id),
      reports: this.reports.filter((r) => !(r.targetType === 'resposta' && r.targetId === id)),
    });
  },
  setReplyHidden(id, hidden) {
    this.commit({ replies: this.replies.map((r) => (r.id === id ? { ...r, hidden } : r)) });
  },
  markSpam(id, reporterId) {
    const report = {
      id: nextId(this.reports),
      targetType: 'resposta',
      targetId: id,
      reporterId,
      reason: 'spam',
      note: 'Marcada como spam pela equipe de moderação.',
      at: new Date().toISOString(),
      resolved: true,
    };
    this.commit({
      replies: this.replies.map((r) => (r.id === id ? { ...r, hidden: true } : r)),
      reports: [...this.reports, report],
    });
  },
  markBest(topicId, replyId) {
    const already = this.replies.find((r) => r.id === replyId)?.best;
    this.commit({
      replies: this.replies.map((r) => {
        if (r.topicId !== topicId) return r;
        if (r.id === replyId) return { ...r, best: !already };
        return r.best ? { ...r, best: false } : r;
      }),
    });
  },

  // ---- Votos (tri-estado) ----
  voteTopic(id, direction) {
    const current = this.topicVotes[id];
    const votes = { ...this.topicVotes };
    const topics = this.topics.map((t) => {
      if (t.id !== id) return t;
      let { upvotes, downvotes } = t;
      if (current === direction) {
        if (direction === 'up') upvotes = Math.max(0, upvotes - 1);
        else downvotes = Math.max(0, downvotes - 1);
      } else if (current) {
        if (direction === 'up') {
          upvotes += 1;
          downvotes = Math.max(0, downvotes - 1);
        } else {
          downvotes += 1;
          upvotes = Math.max(0, upvotes - 1);
        }
      } else if (direction === 'up') upvotes += 1;
      else downvotes += 1;
      return { ...t, upvotes, downvotes };
    });
    if (current === direction) delete votes[id];
    else votes[id] = direction;
    this.topicVotes = votes;
    this.commit({ topics });
  },
  voteReply(id, direction) {
    const current = this.replyVotes[id];
    const votes = { ...this.replyVotes };
    const replies = this.replies.map((r) => {
      if (r.id !== id) return r;
      let { upvotes, downvotes } = r;
      if (current === direction) {
        if (direction === 'up') upvotes = Math.max(0, upvotes - 1);
        else downvotes = Math.max(0, downvotes - 1);
      } else if (current) {
        if (direction === 'up') {
          upvotes += 1;
          downvotes = Math.max(0, downvotes - 1);
        } else {
          downvotes += 1;
          upvotes = Math.max(0, upvotes - 1);
        }
      } else if (direction === 'up') upvotes += 1;
      else downvotes += 1;
      return { ...r, upvotes, downvotes };
    });
    if (current === direction) delete votes[id];
    else votes[id] = direction;
    this.replyVotes = votes;
    this.commit({ replies });
  },
  topicVote(id) {
    return this.topicVotes[id] ?? null;
  },
  replyVote(id) {
    return this.replyVotes[id] ?? null;
  },

  // ---- Denúncias ----
  addReport(targetType, targetId, reporterId, reason, note) {
    const report = {
      id: nextId(this.reports),
      targetType,
      targetId,
      reporterId,
      reason,
      note,
      at: new Date().toISOString(),
      resolved: false,
    };
    this.commit({ reports: [...this.reports, report] });
    return report;
  },
  /** Resolver aplica a moderação: o alvo sai do ar e a denúncia fecha. */
  resolveReport(id) {
    const report = this.reports.find((r) => r.id === id);
    if (!report) return;
    const reports = this.reports.map((r) => (r.id === id ? { ...r, resolved: true } : r));
    if (report.targetType === 'resposta') {
      this.commit({
        reports,
        replies: this.replies.map((r) => (r.id === report.targetId ? { ...r, hidden: true } : r)),
      });
    } else {
      this.commit({
        reports,
        topics: this.topics.map((t) => (t.id === report.targetId ? { ...t, status: 'oculto' } : t)),
      });
    }
  },
  /** Descartar só fecha a denúncia: o conteúdo continua no ar. */
  dismissReport(id) {
    this.commit({ reports: this.reports.map((r) => (r.id === id ? { ...r, resolved: true } : r)) });
  },
  reopenReport(id) {
    this.commit({ reports: this.reports.map((r) => (r.id === id ? { ...r, resolved: false } : r)) });
  },

  // ---- Membros ----
  setMemberRole(id, role) {
    this.commit({ members: this.members.map((m) => (m.id === id ? { ...m, role } : m)) });
  },
  muteMember(id, days) {
    const until = new Date();
    until.setDate(until.getDate() + days);
    this.commit({
      members: this.members.map((m) => (m.id === id ? { ...m, mutedUntil: until.toISOString() } : m)),
    });
  },
  unmuteMember(id) {
    this.commit({ members: this.members.map((m) => (m.id === id ? { ...m, mutedUntil: null } : m)) });
  },
  setMemberBanned(id, banned) {
    this.commit({ members: this.members.map((m) => (m.id === id ? { ...m, banned } : m)) });
  },

  reset() {
    const fresh = seed();
    this.categories = fresh.categories;
    this.topics = fresh.topics;
    this.replies = fresh.replies;
    this.members = fresh.members;
    this.reports = fresh.reports;
    this.topicVotes = {};
    this.replyVotes = {};
    this.persist();
  },

  // ---- Lookups ----
  categoryById(id) {
    return categoryById(this.categories, id) ?? null;
  },
  categoryBySlug(slug) {
    return categoryBySlug(this.categories, slug) ?? null;
  },
  topicById(id) {
    return topicById(this.topics, id) ?? null;
  },
  topicBySlug(slug) {
    return topicBySlug(this.topics, slug) ?? null;
  },
  memberById(id) {
    return memberById(this.members, id) ?? null;
  },
  memberBySlug(slug) {
    return memberBySlug(this.members, slug) ?? null;
  },
  replyById(id) {
    return replyById(this.replies, id) ?? null;
  },
  /** Membro ligado à conta demo — é ele quem vota, responde e é silenciado. */
  get me() {
    const user = Alpine.store('auth') && Alpine.store('auth').user;
    if (!user) return null;
    return memberById(this.members, user.memberId ?? this.currentMemberId) ?? null;
  },
});

// ─── Helpers expostos às fábricas de página ─────────────────────────────────
window.foHelpers = {
  foImage,
  slugify,
  categoryById,
  categoryBySlug,
  topicById,
  topicBySlug,
  memberById,
  memberBySlug,
  replyById,
  score,
  hoursSince,
  daysSince,
  trendingScore,
  isMuted,
  canPost,
  mutedDays,
  visibleTopics,
  topicsOfCategory,
  topicsOfAuthor,
  sortTopics,
  trendingTopics,
  latestTopics,
  unansweredTopics,
  relatedTopics,
  allTags,
  topicText,
  filterTopics,
  repliesOfTopic,
  buildReplyTree,
  repliesToday,
  repliesOfAuthor,
  makeSnippet,
  searchForum,
  BADGE_RULES,
  badgeIcon,
  memberActivity,
  badgesFor,
  topMembers,
  newMembers,
  activityByDay,
  activityByMonth,
  reportRows,
  openReports,
  reportsByReason,
  paginate,
  pageCount,
  pageNumbers,
  relativeParts,
  badgeClass,
  statusColor,
  statusIcon,
  roleColor,
  reasonColor,
  richBody,
  inlineHtml,
  formatNumber: (v) => formatNumber(v ?? 0, i18n.lang),
  formatDate: (iso) => formatDate(iso, i18n.lang),
  formatDateTime: (iso) => formatDateTime(iso, i18n.lang),
  formatTime: (iso) => formatTime(iso, i18n.lang),
};

// ─── Gráficos (mirror fo/ActivityChart.tsx) ─────────────────────────────────
window.foChart = {
  /** Atividade por dia: tópicos abertos e respostas publicadas. */
  activity(series) {
    return {
      chart: { type: 'area', height: 300, stacked: false, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
      series: [
        { name: i18n.t('dashboard.chartTopics'), data: series.topics.map((p) => p.value) },
        { name: i18n.t('dashboard.chartReplies'), data: series.replies.map((p) => p.value) },
      ],
      xaxis: {
        categories: series.topics.map((p) => p.label),
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      yaxis: { labels: { formatter: (v) => String(Math.round(v)) } },
      fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02, shadeIntensity: 1 } },
      stroke: { curve: 'smooth', width: 2 },
      legend: { position: 'top', horizontalAlign: 'right' },
      dataLabels: { enabled: false },
      grid: { borderColor: 'rgba(148,163,184,0.25)', strokeDashArray: 4 },
      colors: ['#465fff', '#12b76a'],
      tooltip: { theme: Alpine.store('ui') && Alpine.store('ui').darkMode ? 'dark' : 'light' },
    };
  },
  /** Atividade do membro por mês, no perfil público. */
  memberActivity(data) {
    return {
      chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
      series: [{ name: i18n.t('member.activity'), data: data.map((p) => p.value) }],
      xaxis: { categories: data.map((p) => p.label), axisBorder: { show: false }, axisTicks: { show: false } },
      yaxis: { labels: { formatter: (v) => String(Math.round(v)) } },
      plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
      dataLabels: { enabled: false },
      grid: { borderColor: 'rgba(148,163,184,0.25)', strokeDashArray: 4 },
      colors: ['#465fff'],
      legend: { show: false },
      tooltip: { theme: Alpine.store('ui') && Alpine.store('ui').darkMode ? 'dark' : 'light' },
    };
  },
};

/**
 * Caixa de gráfico reutilizável: `x-data="foChartBox(() => foChart.activity(series))"`.
 * Recria o gráfico quando o tema ou o idioma mudam.
 */
window.foChartBox = (build) => ({
  chart: null,
  init() {
    this.$nextTick(() => this.render());
    this.$watch('$store.ui.darkMode', () => this.rebuild());
    this.$watch('$store.ui.lang', () => this.rebuild());
  },
  render() {
    if (!this.$el) return;
    this.chart = new ApexCharts(this.$el, build());
    this.chart.render();
  },
  rebuild() {
    if (this.chart) {
      this.chart.destroy();
      this.chart = null;
    }
    this.$el.innerHTML = '';
    this.render();
  },
});

// Aplica as traduções estáticas assim que o Alpine sobe ([data-i18n]).
document.addEventListener('alpine:initialized', () => i18n.applyDom(document));

Alpine.start();
