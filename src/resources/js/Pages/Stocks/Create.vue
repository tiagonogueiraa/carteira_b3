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

const form = useForm({
    ticker: '',
    type: '',
    quantity: '',
    price: '',
    purchased_at: '',
});

const submit = () => {
    form.post(route('stocks.store'));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Cadastrar ação" />

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Cadastrar ativo</CardTitle>
                        <CardDescription>
                            Registre uma compra de ação ou FII na sua carteira
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="ticker">Ticker</Label>
                                    <Input
                                        id="ticker"
                                        v-model="form.ticker"
                                        placeholder="Ex: PETR4"
                                        required
                                    />
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

                                <div class="space-y-2">
                                    <Label for="quantity">Quantidade</Label>
                                    <Input
                                        id="quantity"
                                        type="number"
                                        min="1"
                                        v-model="form.quantity"
                                        required
                                    />
                                    <p v-if="form.errors.quantity" class="text-sm text-destructive">
                                        {{ form.errors.quantity }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="price">Preço unitário</Label>
                                    <Input
                                        id="price"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        v-model="form.price"
                                        required
                                    />
                                    <p v-if="form.errors.price" class="text-sm text-destructive">
                                        {{ form.errors.price }}
                                    </p>
                                </div>

                                <div class="space-y-2 sm:col-span-2">
                                    <Label for="purchased_at">Data da compra</Label>
                                    <Input
                                        id="purchased_at"
                                        type="date"
                                        v-model="form.purchased_at"
                                        required
                                    />
                                    <p v-if="form.errors.purchased_at" class="text-sm text-destructive">
                                        {{ form.errors.purchased_at }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <Button variant="outline" as-child>
                                    <Link :href="route('stocks.index')">Voltar</Link>
                                </Button>                            
                                <Button type="submit" :disabled="form.processing">
                                    Salvar ativo
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>