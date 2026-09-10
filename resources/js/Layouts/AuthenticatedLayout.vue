<script setup>
import { ref, computed } from 'vue';
import Sidebar from '@/Components/Sidebar/Sidebar.vue';
import MobileSidebar from '@/Components/Sidebar/MobileSidebar.vue';
import OrganizationSwitcher from '@/Components/Organizations/Switcher.vue';
import Avatar from '@/Components/ui/Avatar.vue';
import Dropdown from '@/Components/ui/Dropdown.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;
const sidebarCollapsed = ref(false);
const mobileSidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <MobileSidebar :open="mobileSidebarOpen" @close="mobileSidebarOpen = false" />
        <Sidebar :collapsed="sidebarCollapsed" @toggle="sidebarCollapsed = !sidebarCollapsed" />

        <div
            :class="[
                'transition-all duration-300',
                sidebarCollapsed ? 'lg:pl-[72px]' : 'lg:pl-64',
            ]"
        >
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
                <button
                    @click="mobileSidebarOpen = true"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 lg:hidden"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <button
                    @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden lg:inline-flex items-center justify-center rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <OrganizationSwitcher
                    v-if="page.props.auth.current_organization"
                    :organizations="page.props.auth.organizations || []"
                    :current-organization="page.props.auth.current_organization"
                />

                <div class="ml-auto flex items-center gap-3">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-2 rounded-lg p-1.5 text-slate-600 hover:bg-slate-100 transition-colors">
                                <Avatar :name="user.name" size="sm" />
                                <span class="hidden text-sm font-medium sm:block">{{ user.name }}</span>
                                <svg class="hidden h-4 w-4 text-slate-400 sm:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-sm font-medium text-slate-900">{{ user.name }}</p>
                                <p class="text-xs text-slate-500">{{ user.email }}</p>
                            </div>
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
            </header>

            <main class="py-6 sm:px-6 lg:px-8">
                <slot />
            </main>
        </div>
    </div>
</template>
