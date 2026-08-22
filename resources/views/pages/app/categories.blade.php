<x-layout.app>
    @php($field = 'focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body placeholder:text-muted')

    <div x-data="categoriesPage()">
        <x-app.page-header title="categoriesAdmin.title" subtitle="categoriesAdmin.subtitle" :crumbs="[
            ['labelKey' => 'appNav.dashboard', 'href' => route('dashboard')],
            ['labelKey' => 'appNav.categories'],
        ]">
            <x-slot:action>
                <button type="button" @click="openCreate()"
                    class="flex h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
                    <x-icon name="plus" :size="16" />
                    <span x-text="$t('categoriesAdmin.new')"></span>
                </button>
            </x-slot:action>
        </x-app.page-header>

        <template x-if="$store.forum.categories.length === 0">
            <x-ui.empty-state icon="folder" titleKey="categoriesAdmin.emptyTitle" descKey="categoriesAdmin.emptyDesc" />
        </template>

        <div x-show="$store.forum.categories.length > 0" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <template x-for="(category, index) in $store.forum.categories" :key="category.id">
                <article class="surface-card flex flex-col rounded-2xl border p-5"
                    x-data="{ get moderators() { return category.moderatorIds.map(id => $store.forum.memberById(id)).filter(Boolean) } }">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                            :style="{ backgroundColor: category.color + '1a', color: category.color }">
                            <x-fo.category-icon icon="category.icon" :size="22" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-base font-semibold text-heading" x-text="category.name"></h2>
                            <p class="text-xs text-muted"
                                x-text="'/' + category.slug + ' · ' + $t('category.topicCount', { count: category.topicCount })"></p>
                        </div>
                        <div class="flex shrink-0 flex-col gap-1">
                            <button type="button" @click="$store.forum.moveCategoryOrder(category.id, -1)" :disabled="index === 0"
                                :title="$t('categoriesAdmin.moveUp')"
                                class="flex h-6 w-6 items-center justify-center rounded border border-themed text-muted disabled:opacity-30">
                                <x-icon name="chevron-up" :size="12" />
                            </button>
                            <button type="button" @click="$store.forum.moveCategoryOrder(category.id, 1)"
                                :disabled="index === $store.forum.categories.length - 1" :title="$t('categoriesAdmin.moveDown')"
                                class="flex h-6 w-6 items-center justify-center rounded border border-themed text-muted disabled:opacity-30">
                                <x-icon name="chevron-down" :size="12" />
                            </button>
                        </div>
                    </div>

                    <p class="mt-3 line-clamp-3 min-w-0 text-sm text-muted" x-text="category.description"></p>

                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-muted">
                        <template x-for="m in moderators" :key="m.id">
                            <span class="flex items-center gap-1.5 rounded-full border border-themed px-2 py-1">
                                <x-ui.avatar src="m.avatar" name="m.name" size="xs" />
                                <span x-text="m.name"></span>
                            </span>
                        </template>
                        <span x-show="category.staffOnly"
                            class="flex items-center gap-1 rounded-full bg-warning-50 px-2 py-1 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500">
                            <x-icon name="lock" :size="11" />
                            <span x-text="$t('category.staffOnly')"></span>
                        </span>
                    </div>

                    <div class="mt-auto flex items-center gap-2 border-t border-themed pt-4">
                        <button type="button" @click="openEdit(category)"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-1.5 text-xs font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                            <x-icon name="edit" :size="13" />
                            <span x-text="$t('common.edit')"></span>
                        </button>
                        <button type="button" @click="askRemove(category)"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-1.5 text-xs font-medium text-error-600 transition hover:bg-error-50 dark:hover:bg-error-500/10">
                            <x-icon name="trash" :size="13" />
                            <span x-text="$t('common.remove')"></span>
                        </button>
                    </div>
                </article>
            </template>
        </div>

        {{-- Formulário --}}
        <x-ui.modal open="formOpen" close="formOpen = false"
            title="editing ? $t('categoriesAdmin.editTitle') : $t('categoriesAdmin.newTitle')"
            subtitle="$t('categoriesAdmin.formSubtitle')" size="lg">
            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('categoriesAdmin.fieldName')"></label>
                    <input x-model="form.name" class="{{ $field }}" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('categoriesAdmin.fieldDesc')"></label>
                    <textarea rows="3" x-model="form.description" class="{{ $field }} resize-y"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('categoriesAdmin.fieldIcon')"></label>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['megaphone', 'help', 'code', 'palette', 'server', 'coffee', 'star', 'zap', 'folder', 'flag'] as $iconName)
                                <button type="button" @click="form.icon = '{{ $iconName }}'"
                                    class="flex h-10 w-10 items-center justify-center rounded-lg border transition"
                                    :class="form.icon === '{{ $iconName }}' ? 'border-brand-500 text-brand-500' : 'border-themed text-muted'">
                                    <x-icon :name="$iconName" :size="18" />
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('categoriesAdmin.fieldColor')"></label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="form.color" class="h-10 w-16" />
                            <span class="text-sm text-muted" x-text="form.color"></span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('categoriesAdmin.fieldModerators')"></label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="m in staff" :key="m.id">
                            <button type="button" @click="toggleModerator(m.id)"
                                class="flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium transition"
                                :class="form.moderatorIds.includes(m.id) ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-themed text-muted'">
                                <x-ui.avatar src="m.avatar" name="m.name" size="xs" />
                                <span x-text="m.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <label class="flex items-center justify-between gap-3 rounded-lg border border-themed p-3">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('categoriesAdmin.fieldStaffOnly')"></span>
                        <span class="block text-xs text-muted" x-text="$t('categoriesAdmin.fieldStaffOnlyHint')"></span>
                    </span>
                    <x-ui.switch-toggle checked="form.staffOnly" toggle="form.staffOnly = !form.staffOnly" />
                </label>
            </div>
            <x-slot:footer>
                <button type="button" @click="formOpen = false" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="save()" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
                    x-text="$t('common.save')"></button>
            </x-slot:footer>
        </x-ui.modal>

        {{-- Excluir --}}
        <x-ui.modal open="removeTarget !== null" close="removeTarget = null" title="$t('categoriesAdmin.removeTitle')"
            subtitle="removeTarget ? removeTarget.name : ''">
            <p class="text-sm text-muted" x-text="$t('categoriesAdmin.removeDesc')"></p>
            <label class="mt-4 mb-2 block text-sm font-medium text-heading" x-text="$t('categoriesAdmin.moveTopicsTo')"></label>
            <select x-model="moveTo" class="{{ $field }}">
                <template x-for="c in $store.forum.categories.filter(c => !removeTarget || c.id !== removeTarget.id)" :key="c.id">
                    <option :value="String(c.id)" x-text="c.name"></option>
                </template>
            </select>
            <x-slot:footer>
                <button type="button" @click="removeTarget = null" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="doRemove()" class="rounded-lg bg-error-500 px-4 py-2 text-sm font-medium text-white hover:bg-error-600"
                    x-text="$t('common.remove')"></button>
            </x-slot:footer>
        </x-ui.modal>
    </div>

    @push('scripts')
        <script>
            const EMPTY_CATEGORY_FORM = () => ({
                name: '',
                description: '',
                icon: 'folder',
                color: '#465fff',
                staffOnly: false,
                moderatorIds: [],
            });

            function categoriesPage() {
                return {
                    editing: null,
                    formOpen: false,
                    form: EMPTY_CATEGORY_FORM(),
                    removeTarget: null,
                    moveTo: '',
                    get staff() { return $store.forum.members.filter(m => m.role !== 'membro'); },
                    openCreate() {
                        this.editing = null;
                        this.form = EMPTY_CATEGORY_FORM();
                        this.formOpen = true;
                    },
                    openEdit(category) {
                        this.editing = category;
                        this.form = {
                            name: category.name,
                            description: category.description,
                            icon: category.icon,
                            color: category.color,
                            staffOnly: category.staffOnly,
                            moderatorIds: [...category.moderatorIds],
                        };
                        this.formOpen = true;
                    },
                    toggleModerator(id) {
                        this.form.moderatorIds = this.form.moderatorIds.includes(id)
                            ? this.form.moderatorIds.filter(x => x !== id)
                            : [...this.form.moderatorIds, id];
                    },
                    save() {
                        if (!this.form.name.trim()) return;
                        if (this.editing) {
                            $store.forum.updateCategory(this.editing.id, { ...this.form, slug: foHelpers.slugify(this.form.name) });
                            $store.ui.notifySuccess($t('categoriesAdmin.updated'));
                        } else {
                            $store.forum.addCategory({
                                ...this.form,
                                slug: foHelpers.slugify(this.form.name) || 'categoria-' + ($store.forum.categories.length + 1),
                                order: $store.forum.categories.length + 1,
                            });
                            $store.ui.notifySuccess($t('categoriesAdmin.created'));
                        }
                        this.formOpen = false;
                    },
                    askRemove(category) {
                        this.removeTarget = category;
                        const other = $store.forum.categories.find(c => c.id !== category.id);
                        this.moveTo = other ? String(other.id) : '';
                    },
                    doRemove() {
                        if (this.removeTarget) $store.forum.removeCategory(this.removeTarget.id, this.moveTo ? Number(this.moveTo) : null);
                        this.removeTarget = null;
                        $store.ui.notifySuccess($t('categoriesAdmin.removed'));
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
