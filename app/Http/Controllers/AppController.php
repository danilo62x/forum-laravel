<?php

namespace App\Http\Controllers;

use App\Support\FoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Área de moderação (mirror forum/react src/pages/app). */
class AppController extends Controller
{
    public function dashboard(): View
    {
        return view('pages.app.dashboard', ['pageTitle' => 'Visão geral']);
    }

    public function topics(Request $request): View
    {
        return view('pages.app.topics', [
            'pageTitle' => 'Tópicos',
            // ?novo=1 abre o modal de novo tópico já na entrada (mirror React).
            'openNew' => $request->query('novo') === '1',
        ]);
    }

    public function topicDetail(string $id): View
    {
        $topic = FoData::topicById((int) $id);

        return view('pages.app.topic-detail', [
            'pageTitle' => $topic['title'] ?? 'Tópico em moderação',
            'topicId' => (int) $id,
        ]);
    }

    public function reports(): View
    {
        return view('pages.app.reports', ['pageTitle' => 'Denúncias']);
    }

    public function categories(): View
    {
        return view('pages.app.categories', ['pageTitle' => 'Categorias']);
    }

    public function members(): View
    {
        return view('pages.app.members', ['pageTitle' => 'Membros']);
    }

    public function settings(): View
    {
        return view('pages.app.settings', ['pageTitle' => 'Configurações']);
    }
}
