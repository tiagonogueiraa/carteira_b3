<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import VueApexCharts from 'vue3-apexcharts';
import { useDarkMode } from '@/Composables/Usedarkmode';
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

import ptBr from 'apexcharts/dist/locales/pt-br.json';

const props = defineProps({
    stocks: {
        type: Array,
        required: true,
    },
    netWorthHistory: {
        type: Array,
        required: true,
    },
});

const { isDark } = useDarkMode();

// Mesmos valores do :root/.dark do app.css. O ApexCharts desenha em <canvas>/SVG
// próprio, então não enxerga var(--chart-1) direto — por isso os valores ficam
// duplicados aqui, escolhidos com base no isDark em vez de lidos via CSS.
const chartColor = computed(() => (isDark.value ? 'hsl(220 70% 50%)' : 'hsl(12 76% 61%)'));
const mutedColor = computed(() => (isDark.value ? 'hsl(0 0% 63.9%)' : 'hsl(0 0% 45.1%)'));
const borderColor = computed(() => (isDark.value ? 'hsl(0 0% 14.9%)' : 'hsl(0 0% 89.8%)'));

const chartSeries = computed(() => [
    {
        name: 'Capital investido',
        data: props.netWorthHistory.map((point) => point.invested),
    },
]);

const chartOptions = computed(() => ({
    chart: {
        toolbar: { show: false },
        background: 'transparent',
        fontFamily: 'inherit',
        locales: [ptBr],
        defaultLocale: 'pt-br',
    },
    colors: [chartColor.value],
    stroke: {
        curve: 'smooth',
        width: 2,
    },
    markers: {
        size: 4,
        colors: [chartColor.value],
    },
    dataLabels: { enabled: false },
    legend: { show: false },
    grid: {
        borderColor: borderColor.value,
        strokeDashArray: 4,
    },
    xaxis: {
        categories: props.netWorthHistory.map((point) => point.month),
        labels: { style: { colors: mutedColor.value } },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: mutedColor.value },
            formatter: (value) => `R$ ${Number(value).toFixed(0)}`,
        },
    },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (value) => `R$ ${Number(value).toFixed(2)}` },
    },
}));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Minhas ações
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Patrimônio investido</CardTitle>
                        <CardDescription>
                            Capital acumulado nos últimos 6 meses (não inclui
                            valorização de mercado ainda)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <VueApexCharts
                            type="line"
                            height="280"
                            :options="chartOptions"
                            :series="chartSeries"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Minhas ações</CardTitle>
                        <CardDescription>Resumo da carteira</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ticker</TableHead>
                                    <TableHead>Tipo</TableHead>
                                    <TableHead class="text-right">Quantidade</TableHead>
                                    <TableHead class="text-right">Preço médio</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="stocks.length > 0">
                                    <TableRow v-for="stock in stocks" :key="stock.id">
                                        <TableCell class="font-medium">
                                            <Link
                                                :href="route('stocks.show', stock)"
                                                class="hover:underline"
                                            >
                                                {{ stock.ticker }}
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">
                                                {{ stock.type === 'acao' ? 'Ação' : 'FII' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ stock.quantity }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            R$ {{ Number(stock.average_price).toFixed(2) }}
                                        </TableCell>
                                    </TableRow>
                                </template>
                                <TableEmpty v-else :colspan="4">
                                    Nenhuma ação cadastrada ainda.
                                </TableEmpty>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
