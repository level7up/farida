<script setup>
import { ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SidebarItem from './SidebarItem.vue';
import SidebarGroup from './SidebarGroup.vue';
import Avatar from '@/Components/ui/Avatar.vue';
import Dropdown from '@/Components/ui/Dropdown.vue';

const props = defineProps({
    collapsed: Boolean,
});

const emit = defineEmits(['toggle']);

const page = usePage();
const user = page.props.auth.user;

const navigation = [
    {
        label: 'Main',
        items: [
            {
                name: 'Dashboard',
                href: route('dashboard'),
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>',
            },
        ],
    },
    {
        label: 'Organization',
        items: [
            {
                name: 'Invitations',
                href: route('invitations.index', page.props.auth.current_organization?.slug),
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" /><path d="M19 8.839l-7.5 3.75a2.75 2.75 0 01-3 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" /></svg>',
                show: !!page.props.auth.current_organization,
            },
        ],
    },
    {
        label: 'Account',
        items: [
            {
                name: 'Profile',
                href: route('profile.edit'),
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" /></svg>',
            },
        ],
    },
];

const filteredNavigation = navigation.map((group) => ({
    ...group,
    items: group.items.filter((item) => item.show !== false),
}));
</script>

<template>
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-30 flex flex-col border-r border-slate-200 bg-white transition-all duration-300',
            collapsed ? 'w-[72px]' : 'w-64',
        ]"
    >
        <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-4">
            <Link :href="route('dashboard')" class="flex items-center gap-2.5 overflow-hidden">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-600">
                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                </div>
                <span v-if="!collapsed" class="text-lg font-bold text-slate-900">Farida</span>
            </Link>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <template v-for="group in filteredNavigation" :key="group.label">
                <SidebarGroup :label="group.label" :collapsed="collapsed">
                    <SidebarItem
                        v-for="item in group.items"
                        :key="item.name"
                        :href="item.href"
                        :icon="item.icon"
                        :active="route().current(item.name === 'Dashboard' ? 'dashboard' : item.name.toLowerCase() + '*')"
                        :collapsed="collapsed"
                    >
                        {{ item.name }}
                    </SidebarItem>
                </SidebarGroup>
            </template>
        </nav>

        <div class="border-t border-slate-200 p-3">
            <Dropdown align="top" width="48" content-classes="py-1">
                <template #trigger>
                    <button
                        :class="[
                            'flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50',
                            collapsed && 'justify-center px-2',
                        ]"
                    >
                        <Avatar :name="user.name" size="sm" />
                        <span v-if="!collapsed" class="truncate">{{ user.name }}</span>
                        <svg
                            v-if="!collapsed"
                            class="ml-auto h-4 w-4 text-slate-400"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </template>

                <template #content>
                    <Link
                        :href="route('profile.edit')"
                        class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                    >
                        Profile
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                    >
                        Log Out
                    </Link>
                </template>
            </Dropdown>
        </div>
    </aside>
</template>
