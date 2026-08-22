@props([
    'topic' => 'topic',
    'canModerate' => false,
    'canReply' => 'false',
    'highlightId' => 'null',
    'onReply' => '',
    'onReport' => '',
])
{{-- Mirrors forum/react fo/ReplyTree.tsx.
     Respostas em dois níveis. A melhor resposta sobe para o topo e a sub-thread
     pode ser recolhida — o estado de recolhida fica no store de UI (fo-ui). --}}
<div x-data="{
        get topic() { return {{ $topic }} },
        get nodes() { return foHelpers.buildReplyTree($store.forum.replies, this.topic ? this.topic.id : 0, {{ $canModerate ? 'true' : 'false' }}) },
        get highlightId() { return {{ $highlightId }} },
    }">
    <template x-if="nodes.length === 0">
        <x-ui.empty-state icon="message-circle" titleKey="topic.noRepliesTitle" descKey="topic.noRepliesDesc" />
    </template>

    <div x-show="nodes.length > 0" class="space-y-4">
        <template x-for="node in nodes" :key="node.reply.id">
            <div class="space-y-3">
                <x-fo.reply-item reply="node.reply" topic="topic" :depth="0" :canModerate="$canModerate"
                    :canReply="$canReply" highlighted="highlightId === node.reply.id"
                    :onReply="$onReply" :onReport="$onReport" />

                <div x-show="node.children.length > 0" class="ml-3 border-l-2 border-themed pl-3 sm:ml-6 sm:pl-5">
                    <button type="button" @click="$store.ui.toggleThread(node.reply.id)"
                        class="mb-3 inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-muted transition hover:text-brand-600">
                        <template x-if="$store.ui.isThreadCollapsed(node.reply.id)"><x-icon name="chevron-right" :size="13" /></template>
                        <template x-if="!$store.ui.isThreadCollapsed(node.reply.id)"><x-icon name="chevron-down" :size="13" /></template>
                        <span x-text="$store.ui.isThreadCollapsed(node.reply.id)
                            ? $t('topic.showThread', { count: node.children.length })
                            : $t('topic.hideThread', { count: node.children.length })"></span>
                    </button>

                    <div x-show="!$store.ui.isThreadCollapsed(node.reply.id)" class="space-y-3">
                        <template x-for="child in node.children" :key="child.id">
                            <div>
                                <x-fo.reply-item reply="child" topic="topic" :depth="1" :canModerate="$canModerate"
                                    :canReply="$canReply" highlighted="highlightId === child.id"
                                    :onReply="$onReply" :onReport="$onReport" />
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
