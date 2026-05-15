@extends('admin.layout')
@section('title', 'Mega Menu Management')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<!-- Success Alert -->
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div x-data="menuManager()" x-init="fetchMenus()" class="flex gap-6 h-[calc(100vh-8rem)]">
    
    <!-- Left Column: Menus List -->
    <div class="w-1/3 bg-white border border-gray-100 rounded-2xl shadow-sm flex flex-col h-full">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold">Menus</h2>
            <button @click="openMenuModal()" class="text-sm px-3 py-1.5 bg-brand text-white rounded-xl hover:bg-brand-light transition">+ New</button>
        </div>
        <div class="flex-1 overflow-y-auto p-3 space-y-1">
            <template x-if="isLoadingMenus">
                <div class="flex justify-center py-8"><div class="w-6 h-6 border-2 border-accent border-t-transparent rounded-full animate-spin"></div></div>
            </template>
            <template x-for="menu in menus" :key="menu.id">
                <button @click="selectMenu(menu)" class="flex items-center justify-between w-full p-3 rounded-xl text-left transition"
                        :class="activeMenu?.id === menu.id ? 'bg-surface border border-gray-200' : 'hover:bg-gray-50 border border-transparent'">
                    <div>
                        <div class="font-medium text-brand text-sm" x-text="menu.name"></div>
                        <div class="text-xs text-brand-muted mt-0.5" x-text="menu.location"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" :class="menu.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- Right Column: Menu Items Builder -->
    <div class="w-2/3 bg-white border border-gray-100 rounded-2xl shadow-sm flex flex-col h-full">
        <template x-if="!activeMenu">
            <div class="flex-1 flex items-center justify-center text-brand-muted">
                Select a menu to manage its items.
            </div>
        </template>
        
        <template x-if="activeMenu">
            <div class="h-full flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-surface/50 rounded-t-xl">
                    <div class="flex items-center gap-6">
                        <div>
                            <h2 class="font-semibold text-lg" x-text="activeMenu.name"></h2>
                            <p class="text-xs text-brand-muted">Manage items and nested mega menus</p>
                        </div>
                        <div class="flex items-center bg-gray-100 p-1 rounded-lg">
                            <button @click="updateMenuAlignment('left')" 
                                    :class="activeMenu.alignment === 'left' ? 'bg-white shadow-sm text-brand' : 'text-gray-400 hover:text-brand'"
                                    class="px-2 py-1 text-[10px] font-bold uppercase rounded-md transition-all">Left</button>
                            <button @click="updateMenuAlignment('center')" 
                                    :class="activeMenu.alignment === 'center' ? 'bg-white shadow-sm text-brand' : 'text-gray-400 hover:text-brand'"
                                    class="px-2 py-1 text-[10px] font-bold uppercase rounded-md transition-all">Center</button>
                            <button @click="updateMenuAlignment('right')" 
                                    :class="activeMenu.alignment === 'right' ? 'bg-white shadow-sm text-brand' : 'text-gray-400 hover:text-brand'"
                                    class="px-2 py-1 text-[10px] font-bold uppercase rounded-md transition-all">Right</button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="openMenuModal(activeMenu)" class="p-2 text-gray-400 hover:text-brand transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>
                        <button @click="openItemModal()" class="text-sm px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-light transition flex justify-center items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Item</button>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50">
                    <template x-if="isLoadingItems">
                        <div class="flex justify-center py-8"><div class="w-6 h-6 border-2 border-accent border-t-transparent rounded-full animate-spin"></div></div>
                    </template>
                    
                    <div class="space-y-4">
                        <!-- Top Level Items -->
                        <template x-for="item in itemsTree" :key="item.id">
                            <div class="bg-white border text-sm border-gray-200 rounded-[8px] overflow-hidden shadow-sm">
                                <div class="px-4 py-3 flex items-center justify-between bg-white border-b border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-gray-400 cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                        <span class="font-semibold text-brand" x-text="item.label"></span>
                                        <span class="px-2 py-0.5 bg-gray-100 rounded text-[10px] text-brand-muted font-bold tracking-wider uppercase" x-text="item.type"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="openItemModal(item)" class="p-1.5 text-gray-400 hover:text-brand hover:bg-gray-100 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                        <button @click="deleteItem(item.id)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    </div>
                                </div>
                                
                                <!-- Nested Mega Menu Items array -->
                                <template x-if="item.children && item.children.length > 0">
                                    <div class="p-3 bg-gray-50 space-y-2 border-t border-gray-100">
                                        <template x-for="child in item.children" :key="child.id">
                                            <div class="px-4 py-2 bg-white border border-gray-200 shadow-sm rounded-[6px] flex items-center justify-between translate-x-4 w-[calc(100%-1rem)]">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs text-brand min-w-[120px] font-medium" x-text="child.label"></span>
                                                    <template x-if="child.type === 'cta_button'">
                                                        <span class="px-2 py-0.5 bg-accent/20 text-accent-dark rounded text-[10px] font-bold">CTA BUTTON</span>
                                                    </template>
                                                    <template x-if="child.description">
                                                        <span class="text-xs text-gray-400 truncate max-w-[200px]" x-text="child.description"></span>
                                                    </template>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <button @click="openItemModal(child, item.id)" class="p-1 text-gray-400 hover:text-brand transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                                    <button @click="deleteItem(child.id)" class="p-1 text-gray-400 hover:text-red-500 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="itemsTree.length === 0">
                            <div class="text-center py-12 text-brand-muted text-sm pb-10">This menu is empty. Add items to build your mega menu.</div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Item Modal -->
    <div x-show="showItemModal" style="display: none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden" @click.outside="showItemModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="font-bold text-lg" x-text="currentItem.id ? 'Edit Menu Item' : 'New Menu Item'"></h3>
                <button @click="showItemModal = false" class="text-gray-400 hover:text-brand"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-6 overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Label *</label>
                        <input type="text" x-model="currentItem.label" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none" placeholder="E.g. Request a Ride">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Type</label>
                        <select x-model="currentItem.type" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none bg-white">
                            <option value="link">Standard Link</option>
                            <option value="group">Dropdown Group (Mega Menu)</option>
                            <option value="cta_button">CTA Button</option>
                            <option value="divider">Separator Line</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Parent Group</label>
                        <select x-model="currentItem.parent_id" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none bg-white">
                            <option value="">None (Top Level)</option>
                            <template x-for="parent in itemsTree" :key="parent.id">
                                <option :value="parent.id" x-text="parent.label"></option>
                            </template>
                        </select>
                    </div>

                    <template x-if="currentItem.type === 'group' && currentItem.parent_id === ''">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Mega Menu Layout Style</label>
                            <select x-model="currentItem.layout" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none bg-white">
                                <option value="standard">Standard Popover (Uber-Style)</option>
                                <option value="extended_grid">Extended E-Commerce Wide Grid</option>
                                <option value="split_promo">Split Promo (Links + Highlight)</option>
                                <option value="left_showcase">Business Showcase (Image Left)</option>
                                <option value="icon_grid">Dense Icon Tile Grid</option>
                            </select>
                        </div>
                    </template>
                    
                    <template x-if="currentItem.parent_id !== ''">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Grid Column Header (group_label)</label>
                            <input type="text" x-model="currentItem.group_label" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand outline-none" placeholder="E.g. PRODUCTS, FASHION (Extended Grid specific)">
                        </div>
                    </template>
                    
                    <template x-if="currentItem.type !== 'divider'">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">URL Link</label>
                            <input type="text" x-model="currentItem.url" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none" placeholder="#safety or https://...">
                        </div>
                    </template>

                    <div class="col-span-2 relative">
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Description (Sub-text)</label>
                        <input type="text" x-model="currentItem.description" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none" placeholder="Appears below the label in mega menus">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Icon (Name)</label>
                        <input type="text" x-model="currentItem.icon" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand outline-none" placeholder="car, location, star...">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Image URL / Hero Banner</label>
                        <input type="text" x-model="currentItem.image_url" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none" placeholder="Image in mega menu">
                    </div>

                    <!-- Extended metadata section -->
                    <div class="col-span-2 pt-4 mt-2 border-t border-gray-100 flex gap-4">
                        <div class="w-1/2">
                            <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Badge Text (e.g. HOT, TOP)</label>
                            <input type="text" x-model="currentItem.badge_text" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none" placeholder="Leave empty for no badge">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-xs font-semibold text-brand-muted mb-1.5 uppercase">Badge Tailwind Bg Color</label>
                            <input type="text" x-model="currentItem.badge_color" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-sm focus:border-brand focus:ring-0 outline-none" placeholder="bg-lime-400, bg-black...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-surface/50">
                <button @click="showItemModal = false" class="px-4 py-2 text-sm font-semibold hover:bg-gray-200 rounded-[8px] transition">Cancel</button>
                <button @click="saveItem()" class="px-5 py-2 text-sm font-semibold bg-brand text-white rounded-[8px] hover:bg-brand-light transition flex justify-center items-center gap-2">
                    <span x-text="isSaving ? 'Saving...' : 'Save Item'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function menuManager() {
    return {
        menus: [],
        activeMenu: null,
        itemsTree: [],
        isLoadingMenus: false,
        isLoadingItems: false,
        isSaving: false,
        
        showMenuModal: false,
        showItemModal: false,
        
        currentItem: {},
        apiBase: '/api/v1/cms/admin',

        init() {
            // Include token authentication for admin panel
            axios.defaults.withCredentials = true;
            // Since this is a blade view acting as orchestrator UI, we rely on Sanctum/session auth.
        },

        async fetchMenus() {
            this.isLoadingMenus = true;
            try {
                const res = await axios.get(this.apiBase + '/menus');
                this.menus = res.data.data;
                if (this.menus.length > 0 && !this.activeMenu) {
                    this.selectMenu(this.menus[0]);
                }
            } catch (err) {
                alert('Failed to load menus');
            }
            this.isLoadingMenus = false;
        },

        async selectMenu(menu) {
            this.activeMenu = menu;
            this.fetchItems();
        },

        async fetchItems() {
            if (!this.activeMenu) return;
            this.isLoadingItems = true;
            try {
                const res = await axios.get(`${this.apiBase}/menus/${this.activeMenu.id}/items`);
                this.itemsTree = res.data.data;
            } catch (err) {
                alert('Failed to load menu items');
            }
            this.isLoadingItems = false;
        },

        openItemModal(item = null, parentId = '') {
            if (item) {
                this.currentItem = { ...item, parent_id: item.parent_id || '' };
            } else {
                this.currentItem = {
                    label: '', url: '', type: 'link', parent_id: parentId, layout: 'standard',
                    description: '', icon: '', image_url: '', group_label: '', 
                    badge_text: '', badge_color: '', is_active: true
                };
            }
            this.showItemModal = true;
        },

        async saveItem() {
            if (!this.currentItem.label && this.currentItem.type !== 'divider') {
                return alert('Label is required');
            }
            if(this.currentItem.parent_id === "") this.currentItem.parent_id = null;
            
            this.isSaving = true;
            try {
                if (this.currentItem.id) {
                    await axios.put(`${this.apiBase}/items/${this.currentItem.id}`, this.currentItem);
                } else {
                    await axios.post(`${this.apiBase}/menus/${this.activeMenu.id}/items`, this.currentItem);
                }
                this.showItemModal = false;
                this.fetchItems();
            } catch (err) {
                alert('Failed to save item: ' + (err.response?.data?.message || err.message));
            }
            this.isSaving = false;
        },

        async deleteItem(id) {
            if (!confirm('Are you sure you want to delete this item? It will delete nested children too.')) return;
            try {
                await axios.delete(`${this.apiBase}/items/${id}`);
                this.fetchItems();
            } catch (err) {
                alert('Failed to delete');
            }
        },

        async updateMenuAlignment(alignment) {
            if (!this.activeMenu) return;
            const originalAlignment = this.activeMenu.alignment;
            this.activeMenu.alignment = alignment;
            try {
                await axios.put(`${this.apiBase}/menus/${this.activeMenu.id}`, { alignment });
            } catch (err) {
                this.activeMenu.alignment = originalAlignment;
                alert('Failed to update alignment');
            }
        },

        openMenuModal(menu = null) {
            if (menu) {
                this.currentItem = { ...menu };
            } else {
                this.currentItem = { name: '', slug: '', location: 'header', alignment: 'center', is_active: true };
            }
            this.showMenuModal = true;
        }
    }
}
</script>
@endsection
