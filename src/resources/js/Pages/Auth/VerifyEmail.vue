<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter,
} from '@/components/ui/card';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificar email" />

        <Card class="border-zinc-800 bg-zinc-900">
            <CardHeader>
                <CardTitle class="text-zinc-50">Verifique seu email</CardTitle>
                <CardDescription>
                    Obrigado por se cadastrar! Antes de começar, confirme seu
                    endereço de email clicando no link que acabamos de
                    enviar. Se não recebeu o email, podemos enviar outro.
                </CardDescription>
            </CardHeader>

            <CardContent
                v-if="verificationLinkSent"
                class="text-sm font-medium text-emerald-400"
            >
                Um novo link de verificação foi enviado para o email
                cadastrado.
            </CardContent>

            <CardFooter class="justify-between">
                <form @submit.prevent="submit">
                    <Button
                        type="submit"
                        class="bg-emerald-500 text-zinc-950 hover:bg-emerald-400"
                        :disabled="form.processing"
                    >
                        Reenviar email de verificação
                    </Button>
                </form>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-zinc-400 underline hover:text-zinc-200"
                >
                    Sair
                </Link>
            </CardFooter>
        </Card>
    </GuestLayout>
</template>
