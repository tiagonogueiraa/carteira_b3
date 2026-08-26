<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Separator } from '@/components/ui/separator';
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '@/components/ui/avatar';
import {
  Landmark,
  LineChart,
  History,
  PieChart,
  ShieldCheck,
  Lock,
  Check,
} from '@lucide/vue';

// Ícones (biblioteca @lucide/vue) usados hoje nas telas da Minha Carteira —
// ex: os mesmos ícones da seção "Recursos" da Home. Centralizado num array
// pra a seção "Ícones" ficar fácil de manter conforme novos forem usados.
const iconesExemplo = [
  { componente: Landmark, nome: 'Landmark' },
  { componente: LineChart, nome: 'LineChart' },
  { componente: History, nome: 'History' },
  { componente: PieChart, nome: 'PieChart' },
  { componente: ShieldCheck, nome: 'ShieldCheck' },
  { componente: Lock, nome: 'Lock' },
  { componente: Check, nome: 'Check' },
];

// Dados fictícios só para exibir a tabela de exemplo
const ativosExemplo = [
  { ticker: 'PETR4', tipo: 'Ação', quantidade: 100, precoMedio: 32.5, variacao: 2.4 },
  { ticker: 'MXRF11', tipo: 'FII', quantidade: 250, precoMedio: 10.2, variacao: -0.8 },
  { ticker: 'VALE3', tipo: 'Ação', quantidade: 50, precoMedio: 68.1, variacao: 1.1 },
];

const dialogAberto = ref(false);
const tickerSelecionado = ref('');
</script>

<template>
  <Head title="Minha Carteira — Design System" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Minha Carteira — Design System
      </h2>
      <p class="mt-1 text-sm text-gray-500">
        Referência de componentes (shadcn-vue) e ícones (lucide) usados no projeto Minha Carteira.
      </p>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-5xl space-y-10 sm:px-6 lg:px-8">

        <!-- BOTÕES -->
        <Card>
          <CardHeader>
            <CardTitle>Botões</CardTitle>
            <CardDescription>Variantes disponíveis do componente Button</CardDescription>
          </CardHeader>
          <CardContent class="flex flex-wrap gap-3">
            <Button>Padrão</Button>
            <Button variant="secondary">Secundário</Button>
            <Button variant="destructive">Destrutivo</Button>
            <Button variant="outline">Contorno</Button>
            <Button variant="ghost">Ghost</Button>
            <Button variant="link">Link</Button>
            <Button disabled>Desabilitado</Button>
          </CardContent>
        </Card>

        <!-- CARDS -->
        <Card>
          <CardHeader>
            <CardTitle>Cards</CardTitle>
            <CardDescription>Estrutura usada para blocos de resumo (ex: total da carteira)</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <Card>
                <CardHeader>
                  <CardDescription>Valor total investido</CardDescription>
                  <CardTitle class="text-2xl">R$ 12.450,00</CardTitle>
                </CardHeader>
              </Card>
              <Card>
                <CardHeader>
                  <CardDescription>Rentabilidade</CardDescription>
                  <CardTitle class="text-2xl text-emerald-600">+8,3%</CardTitle>
                </CardHeader>
              </Card>
              <Card>
                <CardHeader>
                  <CardDescription>Ativos cadastrados</CardDescription>
                  <CardTitle class="text-2xl">7</CardTitle>
                </CardHeader>
              </Card>
            </div>
          </CardContent>
        </Card>

        <!-- FORMULÁRIO -->
        <Card>
          <CardHeader>
            <CardTitle>Formulário</CardTitle>
            <CardDescription>Input, Label e Select — usados no cadastro de ativos</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="space-y-2">
                <Label for="ticker">Ticker</Label>
                <Input id="ticker" placeholder="Ex: PETR4" v-model="tickerSelecionado" />
              </div>
              <div class="space-y-2">
                <Label for="tipo">Tipo de ativo</Label>
                <Select>
                  <SelectTrigger id="tipo">
                    <SelectValue placeholder="Selecione o tipo" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="acao">Ação</SelectItem>
                    <SelectItem value="fii">FII</SelectItem>
                    <SelectItem value="renda-fixa">Renda Fixa</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
            <Button>Salvar ativo</Button>
          </CardContent>
        </Card>

        <!-- TABELA -->
        <Card>
          <CardHeader>
            <CardTitle>Tabela</CardTitle>
            <CardDescription>Usada para listar os ativos da carteira</CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Ticker</TableHead>
                  <TableHead>Tipo</TableHead>
                  <TableHead class="text-right">Quantidade</TableHead>
                  <TableHead class="text-right">Preço médio</TableHead>
                  <TableHead class="text-right">Variação</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="ativo in ativosExemplo" :key="ativo.ticker">
                  <TableCell class="font-medium">{{ ativo.ticker }}</TableCell>
                  <TableCell>
                    <Badge variant="secondary">{{ ativo.tipo }}</Badge>
                  </TableCell>
                  <TableCell class="text-right">{{ ativo.quantidade }}</TableCell>
                  <TableCell class="text-right">R$ {{ ativo.precoMedio.toFixed(2) }}</TableCell>
                  <TableCell class="text-right">
                    <span :class="ativo.variacao >= 0 ? 'text-emerald-600' : 'text-red-600'">
                      {{ ativo.variacao >= 0 ? '+' : '' }}{{ ativo.variacao }}%
                    </span>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        <!-- BADGES -->
        <Card>
          <CardHeader>
            <CardTitle>Badges</CardTitle>
            <CardDescription>Para indicar tipo de ativo, status, categoria</CardDescription>
          </CardHeader>
          <CardContent class="flex flex-wrap gap-2">
            <Badge>Padrão</Badge>
            <Badge variant="secondary">Secundário</Badge>
            <Badge variant="destructive">Destrutivo</Badge>
            <Badge variant="outline">Contorno</Badge>
          </CardContent>
        </Card>

        <!-- ÍCONES -->
        <Card>
          <CardHeader>
            <CardTitle>Ícones</CardTitle>
            <CardDescription>Biblioteca @lucide/vue — usados nos cards de recursos da Home e em outras telas da Minha Carteira</CardDescription>
          </CardHeader>
          <CardContent class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-7">
            <div
              v-for="icone in iconesExemplo"
              :key="icone.nome"
              class="flex flex-col items-center gap-2 rounded-lg border border-gray-100 p-4 text-center"
            >
              <component :is="icone.componente" class="size-6 text-emerald-600" />
              <span class="text-xs text-gray-500">{{ icone.nome }}</span>
            </div>
          </CardContent>
        </Card>

        <!-- DIALOG (MODAL) -->
        <Card>
          <CardHeader>
            <CardTitle>Dialog (Modal)</CardTitle>
            <CardDescription>Usado para confirmar exclusão de ativo, editar posição, etc.</CardDescription>
          </CardHeader>
          <CardContent>
            <Dialog v-model:open="dialogAberto">
              <DialogTrigger as-child>
                <Button variant="outline">Abrir modal de exemplo</Button>
              </DialogTrigger>
              <DialogContent>
                <DialogHeader>
                  <DialogTitle>Remover ativo</DialogTitle>
                  <DialogDescription>
                    Tem certeza que deseja remover este ativo da sua carteira? Essa ação não pode ser desfeita.
                  </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                  <Button variant="outline" @click="dialogAberto = false">Cancelar</Button>
                  <Button variant="destructive" @click="dialogAberto = false">Remover</Button>
                </DialogFooter>
              </DialogContent>
            </Dialog>
          </CardContent>
        </Card>

        <!-- DROPDOWN MENU -->
        <Card>
          <CardHeader>
            <CardTitle>Dropdown Menu</CardTitle>
            <CardDescription>Usado em menu de ações por linha (editar, excluir, etc.)</CardDescription>
          </CardHeader>
          <CardContent>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline">Abrir menu de ações</Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuLabel>Ações</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem>Editar</DropdownMenuItem>
                <DropdownMenuItem>Duplicar</DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem class="text-red-600">Remover</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </CardContent>
        </Card>

        <!-- TABS -->
        <Card>
          <CardHeader>
            <CardTitle>Tabs</CardTitle>
            <CardDescription>Útil para separar "Ações", "FIIs", "Renda Fixa" numa mesma tela</CardDescription>
          </CardHeader>
          <CardContent>
            <Tabs default-value="acoes">
              <TabsList>
                <TabsTrigger value="acoes">Ações</TabsTrigger>
                <TabsTrigger value="fiis">FIIs</TabsTrigger>
                <TabsTrigger value="renda-fixa">Renda Fixa</TabsTrigger>
              </TabsList>
              <TabsContent value="acoes" class="pt-4 text-sm text-gray-600">
                Conteúdo da aba de Ações.
              </TabsContent>
              <TabsContent value="fiis" class="pt-4 text-sm text-gray-600">
                Conteúdo da aba de FIIs.
              </TabsContent>
              <TabsContent value="renda-fixa" class="pt-4 text-sm text-gray-600">
                Conteúdo da aba de Renda Fixa.
              </TabsContent>
            </Tabs>
          </CardContent>
        </Card>

        <!-- ALERT -->
        <Card>
          <CardHeader>
            <CardTitle>Alertas</CardTitle>
            <CardDescription>Para avisos, erros e confirmações</CardDescription>
          </CardHeader>
          <CardContent class="space-y-3">
            <Alert>
              <AlertTitle>Cotação atualizada</AlertTitle>
              <AlertDescription>
                Os preços foram atualizados pela última vez hoje às 09:00.
              </AlertDescription>
            </Alert>
            <Alert variant="destructive">
              <AlertTitle>Erro ao buscar cotação</AlertTitle>
              <AlertDescription>
                Não foi possível consultar a API no momento. Tente novamente mais tarde.
              </AlertDescription>
            </Alert>
          </CardContent>
        </Card>

        <!-- AVATAR + SEPARATOR -->
        <Card>
          <CardHeader>
            <CardTitle>Avatar e Separador</CardTitle>
          </CardHeader>
          <CardContent class="flex items-center gap-4">
            <Avatar>
              <AvatarImage src="https://github.com/shadcn.png" alt="Avatar" />
              <AvatarFallback>TN</AvatarFallback>
            </Avatar>
            <Separator orientation="vertical" class="h-8" />
            <span class="text-sm text-gray-600">Exemplo de avatar com fallback de iniciais</span>
          </CardContent>
        </Card>

      </div>
    </div>
  </AuthenticatedLayout>
</template>