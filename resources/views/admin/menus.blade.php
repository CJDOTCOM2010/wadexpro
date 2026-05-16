@extends('admin.layout')
@section('title', 'Mega Menu Manager')
@section('content')

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div x-data="menuManager()" x-init="fetchMenus()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Mega Menu Manager</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Build and manage navigation menus, dropdowns, and mega menu layouts.</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Left: Menus List --}}
        <div class="lg:w-72 shrink-0">
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-surface/20 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-brand">Menus</h3>
                    <button @click="openMenuModal()" class="w-7 h-7 bg-brand rounded-lg flex items-center justify-center text-white hover:bg-brand-light transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto" style="max-height: 600px;">
                    <template x-if="isLoadingMenus">
                        <div class="flex justify-center py-8"><div class="w-5 h-5 border-2 border-accent border-t-transparent rounded-full animate-spin"></div></div>
                    </template>
                    <template x-for="menu in menus" :key="menu.id">
                        <button @click="selectMenu(menu)" class="flex items-center justify-between w-full px-5 py-4 text-left transition-colors border-b border-gray-50 last:border-b-0"
                                :class="activeMenu?.id === menu.id ? 'bg-accent/5 border-l-2 border-accent' : 'border-l-2 border-transparent hover:bg-surface/20'">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-brand truncate" x-text="menu.name"></p>
                                <p class="text-[10px] text-brand-muted mt-0.5" x-text="menu.location"></p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-2">
                                <span class="w-1.5 h-1.5 rounded-full" :class="menu.is_active ? 'bg-green-500' : 'bg-red-400'"></span>
                                <button @click.stop="openMenuModal(menu)" class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-brand hover:bg-surface transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                </button>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Right: Menu Items Builder --}}
        <div class="flex-1 min-w-0">
            <template x-if="!activeMenu">
                <div class="bg-white border border-gray-100 rounded-xl flex flex-col items-center justify-center py-20 text-brand-muted">
                    <svg class="w-14 h-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <p class="text-sm font-bold">Select a menu</p>
                    <p class="text-xs mt-1">Choose a menu from the left panel to manage its items.</p>
                </div>
            </template>

            <template x-if="activeMenu">
                <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                    {{-- Toolbar --}}
                    <div class="px-5 py-4 border-b border-gray-100 bg-surface/20">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h3 class="text-sm font-bold text-brand" x-text="activeMenu.name"></h3>
                                    <p class="text-[11px] text-brand-muted">Manage items, nested menus, and layouts</p>
                                </div>
                                <div class="flex items-center gap-0.5 bg-white border border-gray-200 rounded-lg p-0.5">
                                    <button @click="updateMenuAlignment('left')" :class="activeMenu.alignment === 'left' ? 'bg-brand text-white shadow-sm' : 'text-gray-400 hover:text-brand'" class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-colors">Left</button>
                                    <button @click="updateMenuAlignment('center')" :class="activeMenu.alignment === 'center' ? 'bg-brand text-white shadow-sm' : 'text-gray-400 hover:text-brand'" class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-colors">Center</button>
                                    <button @click="updateMenuAlignment('right')" :class="activeMenu.alignment === 'right' ? 'bg-brand text-white shadow-sm' : 'text-gray-400 hover:text-brand'" class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-colors">Right</button>
                                </div>
                            </div>
                            <button @click="openItemModal()" class="px-4 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Item
                            </button>
                        </div>
                    </div>

                    {{-- Items Area --}}
                    <div class="p-5 bg-surface/30 min-h-[400px]">
                        <template x-if="isLoadingItems">
                            <div class="flex justify-center py-12"><div class="w-5 h-5 border-2 border-accent border-t-transparent rounded-full animate-spin"></div></div>
                        </template>

                        <div class="space-y-3">
                            <template x-for="item in itemsTree" :key="item.id">
                                <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                                    {{-- Top-level item header --}}
                                    <div class="px-4 py-3 flex items-center justify-between border-b border-gray-50">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                            <span class="text-sm font-bold text-brand truncate" x-text="item.label"></span>
                                            <span class="px-1.5 py-0.5 bg-surface border border-gray-100 rounded text-[9px] font-bold text-brand-muted tracking-wider uppercase shrink-0" x-text="item.type"></span>
                                            <template x-if="item.badge_text">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold text-white uppercase shrink-0" :style="'background:' + (item.badge_color || '#000')" x-text="item.badge_text"></span>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0 ml-2">
                                            <button @click="openItemModal(item)" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand hover:bg-surface transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                            <button @click="confirmDeleteItem(item.id, item.label)" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Children --}}
                                    <template x-if="item.children && item.children.length > 0">
                                        <div class="p-3 space-y-2">
                                            <template x-for="child in item.children" :key="child.id">
                                                <div class="flex items-center justify-between px-4 py-2.5 bg-surface/50 border border-gray-100 rounded-lg ml-6">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <span class="text-xs font-bold text-brand truncate" x-text="child.label"></span>
                                                        <template x-if="child.type === 'cta_button'">
                                                            <span class="px-1.5 py-0.5 bg-accent/20 text-accent text-[9px] font-bold rounded">CTA</span>
                                                        </template>
                                                        <template x-if="child.description">
                                                            <span class="text-[10px] text-brand-muted truncate hidden sm:inline" x-text="child.description"></span>
                                                        </template>
                                                    </div>
                                                    <div class="flex items-center gap-1 shrink-0 ml-2">
                                                        <button @click="openItemModal(child, item.id)" class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-brand hover:bg-white transition-colors">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                        </button>
                                                        <button @click="confirmDeleteItem(child.id, child.label)" class="w-6 h-6 rounded flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-white transition-colors">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="itemsTree.length === 0 && !isLoadingItems">
                                <div class="flex flex-col items-center justify-center py-12 text-brand-muted">
                                    <svg class="w-10 h-10 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    <p class="text-sm font-bold">This menu is empty</p>
                                    <p class="text-xs mt-1">Click "Add Item" to start building.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Menu Modal --}}
    <div x-show="showMenuModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showMenuModal = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="showMenuModal = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand" x-text="menuForm.id ? 'Edit Menu' : 'New Menu'"></h3>
                        <p class="text-xs text-brand-muted" x-text="menuForm.id ? 'Update menu settings.' : 'Create a new navigation menu.'"></p>
                    </div>
                </div>
                <button @click="showMenuModal = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Menu Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="menuForm.name" placeholder="e.g. Main Header Menu" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Slug</label>
                    <input type="text" x-model="menuForm.slug" placeholder="e.g. main-header" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium font-mono outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Location</label>
                    <select x-model="menuForm.location" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                        <option value="header">Header</option>
                        <option value="footer">Footer</option>
                        <option value="sidebar">Sidebar</option>
                        <option value="mobile">Mobile Menu</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Default Alignment</label>
                    <select x-model="menuForm.alignment" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </div>
                <label class="flex items-center gap-3 p-3.5 bg-surface rounded-lg cursor-pointer hover:bg-accent/5 transition-colors">
                    <input type="checkbox" x-model="menuForm.is_active" class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent/30">
                    <div>
                        <span class="text-sm font-bold text-brand">Active</span>
                        <p class="text-[10px] text-brand-muted">Show this menu on the frontend.</p>
                    </div>
                </label>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showMenuModal = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="button" @click="saveMenu()" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">
                        <span x-text="isSaving ? 'Saving...' : 'Save Menu'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Modal --}}
    <div x-show="showItemModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showItemModal = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showItemModal = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand" x-text="itemForm.id ? 'Edit Menu Item' : 'New Menu Item'"></h3>
                        <p class="text-xs text-brand-muted">Configure label, link, type, and display options.</p>
                    </div>
                </div>
                <button @click="showItemModal = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Label <span class="text-red-500">*</span></label>
                        <input type="text" x-model="itemForm.label" placeholder="e.g. Request a Ride" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Type</label>
                        <select x-model="itemForm.type" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="link">Standard Link</option>
                            <option value="group">Dropdown Group</option>
                            <option value="cta_button">CTA Button</option>
                            <option value="divider">Separator</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Parent Group</label>
                        <select x-model="itemForm.parent_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="">None (Top Level)</option>
                            <template x-for="parent in itemsTree" :key="parent.id">
                                <option :value="parent.id" x-text="parent.label" x-bind:disabled="parent.id === itemForm.id"></option>
                            </template>
                        </select>
                    </div>

                    <template x-if="itemForm.type === 'group' && !itemForm.parent_id">
                        <div class="col-span-2">
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Mega Menu Layout</label>
                            <select x-model="itemForm.layout" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                                <option value="standard">Standard Popover</option>
                                <option value="extended_grid">Extended Wide Grid</option>
                                <option value="split_promo">Split Promo + Links</option>
                                <option value="icon_grid">Icon Tile Grid</option>
                            </select>
                        </div>
                    </template>

                    <template x-if="itemForm.parent_id">
                        <div class="col-span-2">
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Column Header Label</label>
                            <input type="text" x-model="itemForm.group_label" placeholder="e.g. PRODUCTS, SERVICES" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium uppercase outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                        </div>
                    </template>

                    <template x-if="itemForm.type !== 'divider'">
                        <div class="col-span-2">
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">URL</label>
                            <input type="text" x-model="itemForm.url" placeholder="#safety or https://..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                        </div>
                    </template>

                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Description</label>
                        <input type="text" x-model="itemForm.description" placeholder="Sub-text shown below the label" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Icon</label>
                        <input type="text" x-model="itemForm.icon" placeholder="car, star, shield..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Image URL</label>
                        <input type="text" x-model="itemForm.image_url" placeholder="https://..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>

                    <template x-if="itemForm.type !== 'divider'">
                        <div class="col-span-2 grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                            <div>
                                <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Badge Text</label>
                                <input type="text" x-model="itemForm.badge_text" placeholder="e.g. NEW, HOT" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium uppercase outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Badge Color</label>
                                <input type="text" x-model="itemForm.badge_color" placeholder="e.g. #ff0000, bg-red-500" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-surface/20">
                <button @click="showItemModal = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                <button @click="saveItem()" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                    <span x-text="isSaving ? 'Saving...' : 'Save Item'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation --}}
    <div x-show="deleteStep > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDelete()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="closeDelete()">
            <template x-if="deleteStep === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete Item?</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">Permanently delete <strong class="text-brand" x-text="deleteLabel"></strong>? Nested children will also be removed.</p>
                    <div class="flex gap-2">
                        <button type="button" @click="closeDelete()" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                        <button type="button" @click="executeDelete()" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function menuManager() {
    return {
        menus: [], activeMenu: null, itemsTree: [], isLoadingMenus: false, isLoadingItems: false, isSaving: false,
        showMenuModal: false, showItemModal: false,
        menuForm: { id: null, name: '', slug: '', location: 'header', alignment: 'center', is_active: true },
        itemForm: { id: null, label: '', url: '', type: 'link', parent_id: '', layout: 'standard', description: '', icon: '', image_url: '', group_label: '', badge_text: '', badge_color: '', is_active: true },
        deleteStep: 0, deleteId: null, deleteLabel: '',
        apiBase: '/api/v1/cms/admin',

        async fetchMenus() {
            this.isLoadingMenus = true;
            try {
                const res = await axios.get(this.apiBase + '/menus');
                this.menus = res.data.data || [];
                if (this.menus.length > 0 && !this.activeMenu) this.selectMenu(this.menus[0]);
            } catch (e) { console.error('Failed to load menus', e); }
            this.isLoadingMenus = false;
        },
        async selectMenu(menu) {
            this.activeMenu = menu;
            await this.fetchItems();
        },
        async fetchItems() {
            if (!this.activeMenu) return;
            this.isLoadingItems = true;
            try {
                const res = await axios.get(`${this.apiBase}/menus/${this.activeMenu.id}/items`);
                this.itemsTree = res.data.data || [];
            } catch (e) { console.error('Failed to load items', e); }
            this.isLoadingItems = false;
        },
        openMenuModal(menu) {
            if (menu) {
                this.menuForm = { id: menu.id, name: menu.name, slug: menu.slug || '', location: menu.location || 'header', alignment: menu.alignment || 'center', is_active: menu.is_active ?? true };
            } else {
                this.menuForm = { id: null, name: '', slug: '', location: 'header', alignment: 'center', is_active: true };
            }
            this.showMenuModal = true;
        },
        async saveMenu() {
            if (!this.menuForm.name) return alert('Menu name is required.');
            this.isSaving = true;
            try {
                if (this.menuForm.id) {
                    await axios.put(`${this.apiBase}/menus/${this.menuForm.id}`, this.menuForm);
                } else {
                    await axios.post(`${this.apiBase}/menus`, this.menuForm);
                }
                this.showMenuModal = false;
                await this.fetchMenus();
            } catch (e) { alert('Failed to save menu: ' + (e.response?.data?.message || e.message)); }
            this.isSaving = false;
        },
        async updateMenuAlignment(alignment) {
            if (!this.activeMenu) return;
            const orig = this.activeMenu.alignment;
            this.activeMenu.alignment = alignment;
            try { await axios.put(`${this.apiBase}/menus/${this.activeMenu.id}`, { alignment }); }
            catch (e) { this.activeMenu.alignment = orig; }
        },
        openItemModal(item, parentId) {
            if (item) {
                this.itemForm = { id: item.id, label: item.label || '', url: item.url || '', type: item.type || 'link', parent_id: item.parent_id || '', layout: item.layout || 'standard', description: item.description || '', icon: item.icon || '', image_url: item.image_url || '', group_label: item.group_label || '', badge_text: item.badge_text || '', badge_color: item.badge_color || '', is_active: item.is_active ?? true };
            } else {
                this.itemForm = { id: null, label: '', url: '', type: 'link', parent_id: parentId || '', layout: 'standard', description: '', icon: '', image_url: '', group_label: '', badge_text: '', badge_color: '', is_active: true };
            }
            this.showItemModal = true;
        },
        async saveItem() {
            if (this.itemForm.type !== 'divider' && !this.itemForm.label) return alert('Label is required.');
            const payload = { ...this.itemForm };
            if (!payload.parent_id) payload.parent_id = null;
            this.isSaving = true;
            try {
                if (payload.id) {
                    await axios.put(`${this.apiBase}/items/${payload.id}`, payload);
                } else {
                    await axios.post(`${this.apiBase}/menus/${this.activeMenu.id}/items`, payload);
                }
                this.showItemModal = false;
                await this.fetchItems();
            } catch (e) { alert('Failed to save item: ' + (e.response?.data?.message || e.message)); }
            this.isSaving = false;
        },
        confirmDeleteItem(id, label) { this.deleteId = id; this.deleteLabel = label; this.deleteStep = 1; },
        closeDelete() { this.deleteStep = 0; this.deleteId = null; this.deleteLabel = ''; },
        async executeDelete() {
            try {
                await axios.delete(`${this.apiBase}/items/${this.deleteId}`);
                this.closeDelete();
                await this.fetchItems();
            } catch (e) { alert('Failed to delete'); }
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection