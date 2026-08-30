<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from '@/components/ui/card';
import {
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
    TableEmpty,
} from '@/components/ui/table';

defineProps({
    stock: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="stock.ticker" />

        <div class="py-12">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-semibold text-foreground">
                            {{ stock.ticker }}
                        </h1>
                        <Badge variant="secondary">
                            {{ stock.type === 'acao' ? 'Ação' : 'FII' }}
                        </Badge>
                    </div>

                    <div class="flex gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="route('stocks.edit', stock)">Editar</Link>
                        </Button>
                        <Button variant="ghost" as-child>
                            <Link :href="route('stocks.index')">Voltar</Link>
                        </Button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardDescription>Quantidade total</CardDescription>
                            <CardTitle class="text-2xl">{{ stock.quantity }}</CardTitle>
                        </CardHeader>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardDescription>Preço médio</CardDescription>
                            <CardTitle class="text-2xl">
                                R$ {{ Number(stock.average_price).toFixed(2) }}
                            </CardTitle>
                        </CardHeader>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Histórico de compras</CardTitle>
                        <CardDescription>Cada lote registrado nesse ativo</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Data</TableHead>
                                    <TableHead class="text-right">Quantidade</TableHead>
                                    <TableHead class="text-right">Preço</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="stock.lots.length > 0">
                                    <TableRow v-for="lot in stock.lots" :key="lot.id">
                                        <TableCell>
                                            {{ new Date(lot.purchased_at).toLocaleDateString('pt-BR') }}
                                        </TableCell>
                                        <TableCell class="text-right">{{ lot.quantity }}</TableCell>
                                        <TableCell class="text-right">
                                            R$ {{ Number(lot.price).toFixed(2) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            R$ {{ (lot.quantity * lot.price).toFixed(2) }}
                                        </TableCell>
                                    </TableRow>
                                </template>
                                <TableEmpty v-else :colspan="4">
                                    Nenhuma compra registrada.
                                </TableEmpty>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
