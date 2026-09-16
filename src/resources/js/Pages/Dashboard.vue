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

import { Wallet, LineChart, TrendingUp, TrendingDown, HandCoins } from '@lucide/vue';

import ptBr from 'apexcharts/dist/locales/pt-br.json';

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});
const currencyFormatterCompact = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    maximumFractionDigits: 0,
});

const props = defineProps({
    stocks: {
        type: Array,
        required: true,
    },
    netWorthHistory: {
        type: Array,
        required: true,
    },
    stocksHistory: {
        type: Object,
        required: true,
    },
});


console.log(props);
console.log(props.stocks);
console.log(props.netWorthHistory);
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
            formatter: (value) => currencyFormatterCompact.format(Number(value)),
        },
    },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (value) => currencyFormatter.format(Number(value)) },
    },
}));

const chartSeriesAcoesGroup = computed(() => props.stocksHistory.series);

const chartAcoesGroup = computed(() => ({
    chart: {
        toolbar: { show: false },
        background: 'transparent',
        fontFamily: 'inherit',
        locales: [ptBr],
        defaultLocale: 'pt-br',
    },
    stroke: {
        curve: 'smooth',
        width: 2,
    },
    markers: {
        size: 4,
    },
    dataLabels: { enabled: false },
    legend: {
        show: true,
        labels: { colors: mutedColor.value },
    },
    grid: {
        borderColor: borderColor.value,
        strokeDashArray: 4,
    },
    xaxis: {
        categories: props.stocksHistory.categories,
        labels: { style: { colors: mutedColor.value } },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: mutedColor.value },
            formatter: (value) => currencyFormatterCompact.format(Number(value)),
        },
    },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (value) => currencyFormatter.format(Number(value)) },
    },
}));


const totalInvestido = computed(() =>
    props.stocks.reduce(
        (sum, stock) => sum + stock.quantity * Number(stock.average_price),
        0
    )
);

const totalMercado = computed(() =>
    props.stocks.reduce((sum, stock) => {
        const precoAtual = stock.market?.regular_market_price ?? stock.average_price;
        return sum + stock.quantity * Number(precoAtual) ;
    }, 0)
);



// TOTAL DIVIDENDOS
const totalDividendos = computed(() =>
props.stocks.reduce((sum, stock) => sum + Number(stock.total_dividends), 0)
);

// SOMA TOTAIS TABELAS
const stocksComTotais = computed(() =>
    props.stocks.map((stock) => {
        const investido = stock.quantity * Number(stock.average_price);
        const precoAtual = stock.market?.regular_market_price ?? stock.average_price;
        const mercado = stock.quantity * Number(precoAtual);
        const percentual = investido === 0 ? 0 : ((mercado - investido) / investido) * 100;
        const dividendos = stock.total_dividends;
        
        return { ...stock, investido, mercado, percentual, dividendos };
    })
);

// total com dividendos
const totalComDividendos = computed(() => totalMercado.value + totalDividendos.value);

const percentualGanho = computed(() => {
    if (totalInvestido.value === 0) return 0;
    return ((totalComDividendos.value - totalInvestido.value) / totalInvestido.value) * 100;
});
console.log('total mercado  ', totalMercado.value);
console.log('total dividendos', totalDividendos.value);

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
            <div class="p-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl grid gap-4 sm:grid-cols-5">
                   <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Total investido</CardDescription>
                            <Wallet class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>{{ currencyFormatter.format(totalInvestido) }}</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Valor de mercado</CardDescription>
                            <LineChart class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>{{ currencyFormatter.format(totalMercado) }}</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Valor com dividendos</CardDescription>
                            <HandCoins class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>{{ currencyFormatter.format(totalComDividendos) }}</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Rentabilidade</CardDescription>
                            <TrendingUp class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle :class="percentualGanho >= 0 ? 'text-green-600' : 'text-red-600'">
                                {{ percentualGanho >= 0 ? '+' : '' }}{{ percentualGanho.toFixed(2) }}%
                            </CardTitle>             
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Total em dividendos</CardDescription>
                            <TrendingDown class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>
                                {{ currencyFormatter.format(totalDividendos) }}
                            </CardTitle>
                        </CardContent>                        
                    </Card>
                </div>
            </div>
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
                        <CardTitle>Patrimônio investido AÇÔES</CardTitle>
                        <CardDescription>
                            Capital acumulado nos últimos 6 meses (não inclui
                            valorização de mercado ainda)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <VueApexCharts
                            type="line"
                            height="280"
                            :options="chartAcoesGroup"
                            :series="chartSeriesAcoesGroup"
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
                                    <TableHead class="text-right">Preço de mercado</TableHead>
                                    <TableHead class="text-right">Total investido</TableHead>
                                    <TableHead class="text-right">Total mercado</TableHead>
                                    <TableHead class="text-right">Rentabilidade</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="stocksComTotais.length > 0">
                                    <TableRow v-for="stock in stocksComTotais" :key="stock.id">
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
                                            {{ currencyFormatter.format(Number(stock.average_price)) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ stock.market?.regular_market_price
                                                ? currencyFormatter.format(Number(stock.market.regular_market_price))
                                                : '—' }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ currencyFormatter.format(stock.investido) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ currencyFormatter.format(stock.mercado) }}
                                        </TableCell>
                                        <TableCell
                                            class="text-right"
                                            :class="stock.percentual >= 0 ? 'text-green-600' : 'text-red-600'"
                                        >
                                            {{ stock.percentual >= 0 ? '+' : '' }}{{ stock.percentual.toFixed(2) }}%
                                        </TableCell>

                                    </TableRow>
                                </template>
                                <TableEmpty v-else :colspan="8">
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
