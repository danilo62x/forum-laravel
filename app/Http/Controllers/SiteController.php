<?php

namespace App\Http\Controllers;

use App\Support\FoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Fórum público. Os dados vivem no store Alpine (fo-data) para que voto,
 * melhor resposta e moderação sejam reativos; aqui o servidor resolve o
 * parâmetro da rota, o título da página e o que é preciso para o primeiro
 * paint (capa da categoria, filtros vindos da query string).
 */
class SiteController extends Controller
{
    public function home(): View
    {
        return view('pages.home', ['pageTitle' => null]);
    }

    public function category(string $slug): View
    {
        $category = FoData::categoryBySlug($slug);

        return view('pages.category', [
            'pageTitle' => $category['name'] ?? 'Categoria não encontrada',
            'slug' => $slug,
            'category' => $category,
            'cover' => $category ? FoData::foImage('categoria-'.$category['slug'], 1600, 420) : null,
        ]);
    }

    public function topic(string $slug): View
    {
        $topic = FoData::topicBySlug($slug);

        return view('pages.topic', [
            'pageTitle' => $topic['title'] ?? 'Tópico não encontrado',
            'slug' => $slug,
        ]);
    }

    public function member(string $slug): View
    {
        $member = FoData::memberBySlug($slug);

        return view('pages.member', [
            'pageTitle' => $member['name'] ?? 'Membro não encontrado',
            'slug' => $slug,
        ]);
    }

    public function search(Request $request): View
    {
        return view('pages.search', [
            'pageTitle' => 'Buscar no fórum',
            'query' => (string) $request->query('q', ''),
            'categoryParam' => (string) $request->query('categoria', 'all'),
            'tagParam' => (string) $request->query('tag', 'all'),
            'unansweredParam' => $request->query('sem-resposta') === '1',
        ]);
    }

    public function rules(): View
    {
        return view('pages.rules', ['pageTitle' => 'Diretrizes da comunidade']);
    }
}
