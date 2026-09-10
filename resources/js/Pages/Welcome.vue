<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-white">
        <nav class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <Link href="/" class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-slate-900">Farida</span>
                    </Link>

                    <div v-if="canLogin" class="flex items-center gap-3">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')">
                                <Button variant="ghost">Dashboard</Button>
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')">
                                <Button variant="ghost">Log in</Button>
                            </Link>
                            <Link v-if="canRegister" :href="route('register')">
                                <Button>Get started</Button>
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pt-16">
            <section class="relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-50 via-white to-brand-100/30" />
                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
                    <div class="text-center max-w-3xl mx-auto">
                        <div class="inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-1.5 text-sm font-medium text-brand-700 mb-6">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-brand-500"></span>
                            </span>
                            Property Management Made Simple
                        </div>
                        <h1 class="text-4xl sm:text-6xl font-bold text-slate-900 tracking-tight mb-6">
                            Manage your properties
                            <span class="text-brand-600">with confidence</span>
                        </h1>
                        <p class="text-lg sm:text-xl text-slate-600 mb-10 max-w-2xl mx-auto">
                            Farida helps you manage properties, coordinate teams, and streamline operations — all from one powerful dashboard.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link v-if="canRegister" :href="route('register')">
                                <Button size="lg" class="w-full sm:w-auto">
                                    Start for free
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </Button>
                            </Link>
                            <Link :href="route('login')">
                                <Button variant="secondary" size="lg" class="w-full sm:w-auto">
                                    Sign in to your account
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-24 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-slate-900 mb-4">Everything you need</h2>
                        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                            Built for property managers who want to focus on what matters.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition-shadow">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 mb-5">
                                <svg class="h-6 w-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Property Management</h3>
                            <p class="text-slate-600">
                                Track properties, units, and maintenance requests all in one place.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition-shadow">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 mb-5">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Team Collaboration</h3>
                            <p class="text-slate-600">
                                Invite team members, assign roles, and collaborate seamlessly.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition-shadow">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 mb-5">
                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Financial Tracking</h3>
                            <p class="text-slate-600">
                                Track rent payments, expenses, and generate financial reports.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition-shadow">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 mb-5">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Scheduling</h3>
                            <p class="text-slate-600">
                                Manage inspections, maintenance schedules, and tenant communications.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition-shadow">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 mb-5">
                                <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Secure & Reliable</h3>
                            <p class="text-slate-600">
                                Enterprise-grade security with role-based access control.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition-shadow">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-100 mb-5">
                                <svg class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Analytics</h3>
                            <p class="text-slate-600">
                                Gain insights with real-time analytics and customizable dashboards.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-24 bg-slate-50">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Ready to get started?</h2>
                    <p class="text-lg text-slate-600 mb-8 max-w-2xl mx-auto">
                        Join property managers who trust Farida to streamline their operations.
                    </p>
                    <Link v-if="canRegister" :href="route('register')">
                        <Button size="lg">
                            Create your free account
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </Button>
                    </Link>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex items-center justify-center gap-2.5 mb-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-slate-900">Farida</span>
                </div>
                <p class="text-sm text-slate-500">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </p>
            </div>
        </footer>
    </div>
</template>
