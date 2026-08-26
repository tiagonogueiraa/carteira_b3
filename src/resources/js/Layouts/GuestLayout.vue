<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <!--
        Mesmo fundo escuro + acento verde da Home (Welcome.vue), pra Login e
        Registro parecerem parte do mesmo produto, não uma tela solta do
        Breeze. A classe "dark" na raiz ativa as variantes dark: dos
        componentes shadcn-vue (Input, Button, Card) usados dentro do slot —
        sem ela, esses componentes renderizam com o estilo de tema claro.
    -->
    <div
        class="dark flex min-h-screen flex-col items-center justify-center gap-6 bg-zinc-950 px-6 py-12 text-zinc-50"
    >
        <Link href="/" class="text-lg font-semibold tracking-tight">
            Minha Carteira
        </Link>

        <div class="w-full sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
