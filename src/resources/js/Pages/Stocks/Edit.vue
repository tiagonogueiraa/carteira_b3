<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from '@/components/ui/card';

const props = defineProps({
    stock: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    ticker: props.stock.ticker,
    type: props.stock.type,
});

const submit = () => {
    form.put(route('stocks.update', props.stock));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Editar ação" />

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Editar ativo</CardTitle>
                        <CardDescription>
                            Ajuste o ticker ou o tipo do ativo
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="ticker">Ticker</Label>
                                    <Input id="ticker" v-model="form.ticker" required />
                                    <p v-if="form.errors.ticker" class="text-sm text-destructive">
                                        {{ form.errors.ticker }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="type">Tipo de ativo</Label>
                                    <Select v-model="form.type">
                                        <SelectTrigger id="type" class="w-full">
                                            <SelectValue placeholder="Selecione o tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="acao">Ação</SelectItem>
                                            <SelectItem value="fii">FII</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.type" class="text-sm text-destructive">
                                        {{ form.errors.type }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <Button variant="outline" as-child>
                                    <Link :href="route('stocks.index')">Voltar</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    Salvar alterações
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
