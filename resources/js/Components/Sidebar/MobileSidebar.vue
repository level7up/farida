<script setup>
import { watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SidebarItem from './SidebarItem.vue';
import SidebarGroup from './SidebarGroup.vue';
import Avatar from '@/Components/ui/Avatar.vue';

const props = defineProps({
    open: Boolean,
});

const emit = defineEmits(['close']);

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

watch(() => page.url, () => emit('close'));
</script>

<template>
    <div v-show="open" class="relative z-40 lg:hidden">
        <Transition
            enter-active-class="transition-opacity duration-300 ease-linear"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300 ease-linear"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="open"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
                @click="emit('close')"
            />
        </Transition>

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <div
                v-show="open"
                class="fixed inset-y-0 left-0 z-40 w-72 overflow-y-auto bg-white"
            >
                <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-4">
                    <Link :href="route('dashboard')" class="flex items-center gap-2.5" @click="emit('close')">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-slate-900">Farida</span>
                    </Link>
                </div>

                <nav class="px-3 py-4">
                    <template v-for="group in filteredNavigation" :key="group.label">
                        <SidebarGroup :label="group.label">
                            <SidebarItem
                                v-for="item in group.items"
                                :key="item.name"
                                :href="item.href"
                                :icon="item.icon"
                                :active="route().current(item.name === 'Dashboard' ? 'dashboard' : item.name.toLowerCase() + '*')"
                            >
                                {{ item.name }}
                            </SidebarItem>
                        </SidebarGroup>
                    </template>
                </nav>

                <div class="border-t border-slate-200 p-4">
                    <div class="flex items-center gap-3">
                        <Avatar :name="user.name" size="sm" />
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-900 truncate">{{ user.name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <Link
                            :href="route('profile.edit')"
                            class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                            @click="emit('close')"
                        >
                            Profile
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full text-left rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                            @click="emit('close')"
                        >
                            Log Out
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
