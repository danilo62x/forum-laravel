@props(['row' => 'row'])
{{-- Mirrors forum/react fo/ReportRow.tsx.
     O link leva ao alvo — quando a denúncia é de uma resposta, a página abre
     já rolada até a âncora dela. --}}
<div x-data="{
        get row() { return {{ $row }} },
        get report() { return this.row.report },
        get topic() { return this.row.topic },
        get reply() { return this.row.reply },
        get reporter() { return this.row.reporter },
        get href() {
            if (!this.topic) return '/app/denuncias';
            return this.reply
                ? '/app/topicos/' + this.topic.id + '#resposta-' + this.reply.id
                : '/app/topicos/' + this.topic.id;
        },
        get excerpt() {
            const text = this.reply ? this.reply.body : (this.topic ? this.topic.body : '');
            return text.replace(/\s+/g, ' ').slice(0, 160);
        },
    }"
    class="surface-card rounded-xl border p-4" :class="report.resolved ? 'opacity-70' : ''">
    <div class="flex flex-wrap items-center gap-2">
        <x-fo.reason-pill reason="report.reason" />
        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <template x-if="report.targetType === 'topico'"><x-icon name="message-square" :size="12" /></template>
            <template x-if="report.targetType !== 'topico'"><x-icon name="corner-down-right" :size="12" /></template>
            <span x-text="$t('reports.target.' + report.targetType)"></span>
        </span>
        <span x-show="report.resolved" class="inline-flex items-center gap-1 text-xs text-success-600 dark:text-success-500">
            <x-icon name="check" :size="12" :strokeWidth="2.4" />
            <span x-text="$t('reports.resolved')"></span>
        </span>
        <span class="ml-auto text-xs text-muted" x-text="$datetime(report.at)"></span>
    </div>

    <h3 class="mt-3 text-sm font-semibold text-heading">
        <a :href="href" class="transition hover:text-brand-600 dark:hover:text-brand-400"
            x-text="topic ? topic.title : $t('reports.targetGone')"></a>
    </h3>

    <p x-show="excerpt" class="mt-1 min-w-0 text-xs break-words text-muted" x-text="excerpt + '…'"></p>

    <div class="mt-3 flex flex-wrap items-start gap-2 rounded-lg bg-gray-50 p-3 dark:bg-white/5">
        <template x-if="reporter">
            <x-ui.avatar src="reporter.avatar" name="reporter.name" size="xs" />
        </template>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-medium text-heading" x-text="$t('reports.reportedBy', { name: reporter ? reporter.name : '—' })"></p>
            <p class="mt-0.5 text-xs break-words text-muted" x-text="report.note"></p>
        </div>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <a :href="href"
            class="inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-1.5 text-xs font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
            <x-icon name="eye" :size="13" />
            <span x-text="$t('reports.openTarget')"></span>
        </a>
        <template x-if="report.resolved">
            <button type="button" @click="$store.forum.reopenReport(report.id); $store.ui.notifySuccess($t('reports.reopenedToast'))"
                class="inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-1.5 text-xs font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                <x-icon name="refresh-cw" :size="13" />
                <span x-text="$t('reports.reopen')"></span>
            </button>
        </template>
        <template x-if="!report.resolved">
            <span class="flex flex-wrap items-center gap-2">
                <button type="button" @click="$store.forum.resolveReport(report.id); $store.ui.notifySuccess($t('reports.resolvedToast'))"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-brand-600">
                    <x-icon name="shield-check" :size="13" />
                    <span x-text="$t('reports.resolve')"></span>
                </button>
                <button type="button" @click="$store.forum.dismissReport(report.id); $store.ui.notifySuccess($t('reports.dismissedToast'))"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-1.5 text-xs font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                    <x-icon name="x" :size="13" />
                    <span x-text="$t('reports.dismiss')"></span>
                </button>
            </span>
        </template>
    </div>
</div>
