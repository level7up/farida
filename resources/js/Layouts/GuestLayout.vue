<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen">
        <div class="hidden w-1/2 bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 lg:flex lg:flex-col lg:items-center lg:justify-center lg:p-12">
            <Link href="/" class="flex items-center gap-3 mb-8">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-white">Farida</span>
            </Link>

            <div class="max-w-md text-center">
                <h1 class="text-3xl font-bold text-white mb-4">
                    Property Management Simplified
                </h1>
                <p class="text-lg text-brand-100">
                    Manage your properties, teams, and operations all in one place.
                </p>
            </div>

            <div class="mt-12 grid grid-cols-2 gap-4 max-w-sm">
                <div class="rounded-xl bg-white/10 backdrop-blur-sm p-4 text-center">
                    <div class="text-2xl font-bold text-white">100+</div>
                    <div class="text-sm text-brand-100">Properties</div>
                </div>
                <div class="rounded-xl bg-white/10 backdrop-blur-sm p-4 text-center">
                    <div class="text-2xl font-bold text-white">50+</div>
                    <div class="text-sm text-brand-100">Teams</div>
                </div>
            </div>
        </div>

        <div class="flex w-full items-center justify-center p-6 sm:p-12 lg:w-1/2">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <Link href="/" class="flex items-center gap-2.5 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600">
                            <svg class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-slate-900">Farida</span>
                    </Link>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
