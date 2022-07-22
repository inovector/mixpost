<template>
    <div class="flex flex-col h-screen min-h-full">
        <header class="bg-white shadow" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>
        <main class="grow">
            <slot />
        </main>
        <footer>2022</footer>
    </div>
</template>
