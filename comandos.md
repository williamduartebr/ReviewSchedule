# 🚀 Comandos Corrigidos - Exemplos de Uso

## 📊 1. Analisar o CSV Primeiro

Antes de gerar artigos, analise o CSV para entender a distribuição dos veículos:

```bash
# Estatísticas básicas
php artisan review-schedule:csv-stats data/todos_veiculos.csv

# Estatísticas detalhadas (mostra todas as marcas)
php artisan review-schedule:csv-stats data/todos_veiculos.csv --detailed

# Preview de carros elétricos
php artisan review-schedule:csv-stats data/todos_veiculos.csv --vehicle-type=electric

# Preview de uma marca específica
php artisan review-schedule:csv-stats data/todos_veiculos.csv --make=BMW
```

## 🎯 2. Gerar Artigos por Tipo de Veículo

### Carros Elétricos (para testar template elétrico)
```bash
# Dry run - ver o que seria gerado
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=5 --dry-run

# Gerar 10 carros elétricos
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=10 --batch=5

# Gerar com validação rigorosa
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=5 --template-validation
```

### Carros Híbridos
```bash
# Gerar carros híbridos
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=hybrid --limit=8 --batch=4

# Dry run para híbridos
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=hybrid --limit=5 --dry-run
```

### Motocicletas
```bash
# Gerar motocicletas
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=motorcycle --limit=10 --batch=5

# Motos a partir da linha 50
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=motorcycle --start=50 --limit=15
```

### Carros Convencionais
```bash
# Gerar carros convencionais
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=car --limit=20 --batch=10

# Carros de uma marca específica
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=car --make=Chevrolet --limit=15
```

## 🔍 3. Filtros Específicos

### Por Marca
```bash
# Apenas BMW
php artisan review-schedule:generate data/todos_veiculos.csv --make=BMW --limit=10

# Apenas Tesla
php artisan review-schedule:generate data/todos_veiculos.csv --make=Tesla --limit=5

# Apenas Ducati (motocicletas)
php artisan review-schedule:generate data/todos_veiculos.csv --make=Ducati --limit=8
```

### Por Ano
```bash
# Veículos de 2020 a 2025
php artisan review-schedule:generate data/todos_veiculos.csv --year=2020-2025 --limit=25

# Apenas 2024
php artisan review-schedule:generate data/todos_veiculos.csv --year=2024 --limit=20

# Carros elétricos de 2023-2025
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --year=2023-2025 --limit=12
```

### Combinando Filtros
```bash
# BMW elétricos de 2022-2025
php artisan review-schedule:generate data/todos_veiculos.csv --make=BMW --vehicle-type=electric --year=2022-2025 --limit=8

# Motos Yamaha
php artisan review-schedule:generate data/todos_veiculos.csv --make=Yamaha --vehicle-type=motorcycle --limit=10
```

## 🧪 4. Testes e Debug

### Dry Run (não salva, só mostra o que faria)
```bash
# Ver o que seria gerado sem salvar
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=3 --dry-run
```

### Debug de geração
```bash
# Debug de um veículo específico
php artisan review-schedule:debug data/todos_veiculos.csv --vehicle=1 --detailed

# Debug do segundo veículo
php artisan review-schedule:debug data/todos_veiculos.csv --vehicle=2
```

### Force (regenerar existentes)
```bash
# Regenerar artigos existentes
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=5 --force
```

## 📈 5. Validação de Templates

### Testar cada template separadamente
```bash
# Testar template elétrico - deve conter apenas termos elétricos
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=3 --dry-run

# Testar template de carro - deve conter apenas termos de combustão
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=car --limit=3 --dry-run

# Testar template híbrido - deve conter ambos os termos
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=hybrid --limit=3 --dry-run

# Testar template moto - deve conter termos específicos de moto
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=motorcycle --limit=3 --dry-run
```

## 🔄 6. Fluxo de Teste Recomendado

### Passo 1: Analisar dados
```bash
php artisan review-schedule:csv-stats data/todos_veiculos.csv --detailed
```

### Passo 2: Testar um tipo por vez (dry run)
```bash
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=2 --dry-run
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=car --limit=2 --dry-run
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=hybrid --limit=2 --dry-run
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=motorcycle --limit=2 --dry-run
```

### Passo 3: Gerar pequenos lotes
```bash
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=3 --batch=3
```

### Passo 4: Analisar artigos gerados
```bash
php artisan review-schedule:analyze-simple --limit=5
```

### Passo 5: Se estiver tudo ok, gerar mais
```bash
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=20 --batch=5
```

## ⚠️ 6. Solução de Problemas

### Se o limite não funcionar
```bash
# Verificar se está usando o comando corrigido
php artisan list | grep review-schedule

# Deve mostrar a nova signature com --limit
php artisan review-schedule:generate --help
```

### Se templates estiverem misturados
```bash
# Usar validação rigorosa (ainda não implementada, mas planejada)
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=3 --template-validation

# Analisar artigos quebrados
php artisan review-schedule:analyze-simple --limit=10
```

### Se houver muitos erros
```bash
# Usar modo seguro com tratamento de erro melhorado
php artisan review-schedule:safe-generate data/todos_veiculos.csv --limit=10 --batch=5 --validate-only

# Debug de um veículo específico
php artisan review-schedule:debug data/todos_veiculos.csv --vehicle=1
```

## 📋 7. Comandos de Verificação

### Estatísticas atuais
```bash
# Ver quantos artigos existem
php artisan review-schedule:stats

# Analisar estrutura dos artigos
php artisan review-schedule:debug-structure --limit=5
```

### Limpeza e reset
```bash
# Resetar sincronizações futuras (se necessário)
php artisan review-schedule:reset-future-sync

# Analisar problemas focados
php artisan review-schedule:analyze-focused --limit=20
```

## 🎯 8. Cenários de Uso Específicos

### Para desenvolvimento/teste
```bash
# Gerar apenas 1 de cada tipo para teste rápido
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=1 --dry-run
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=car --limit=1 --dry-run
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=hybrid --limit=1 --dry-run
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=motorcycle --limit=1 --dry-run
```

### Para produção controlada
```bash
# Gerar em pequenos lotes com verificação
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=50 --batch=10
php artisan review-schedule:analyze-simple --limit=10  # Verificar qualidade
# Se ok, continuar com próximo tipo...
```

### Para correção de problemas
```bash
# Regenerar artigos com problemas (force mode)
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=20 --force

# Auto-fix para problemas críticos
php artisan review-schedule:auto-fix --dry-run --vehicle-type=electric --limit=10
```

## 🔧 9. Novos Recursos Implementados

### ✅ Funcionando agora:
- `--limit` funcional em todos os comandos
- `--vehicle-type` para filtrar por tipo
- `--make` para filtrar por marca  
- `--year` para filtrar por ano ou intervalo
- `--start` para começar de uma linha específica
- `--dry-run` melhorado com preview
- `csv-stats` para análise prévia

### 🚧 Em implementação:
- `--template-validation` para validação rigorosa
- Detecção automática de mistura de conteúdo
- Alertas quando template não corresponde ao tipo

### 📝 Exemplo de workflow completo:
```bash
# 1. Analisar CSV
php artisan review-schedule:csv-stats data/todos_veiculos.csv

# 2. Preview carros elétricos
php artisan review-schedule:csv-stats data/todos_veiculos.csv --vehicle-type=electric

# 3. Teste dry-run
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=2 --dry-run

# 4. Gerar pequeno lote
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=5 --batch=5

# 5. Verificar qualidade
php artisan review-schedule:analyze-simple --limit=5

# 6. Se ok, gerar mais
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=20
```

## 💡 Dicas Importantes

1. **Sempre use `csv-stats` primeiro** para entender a distribuição dos dados
2. **Use `--dry-run`** para testar antes de gerar
3. **Comece com `--limit` pequeno** (5-10) para testar
4. **Teste um tipo de veículo por vez** para isolar problemas
5. **Use `--batch` pequeno** (5-10) para melhor controle de erro
6. **Combine filtros** para ser mais específico (ex: `--make=BMW --vehicle-type=electric`)

## 🎪 Comandos Mais Úteis Para Seu Caso

```bash
# Para testar rapidamente se funcionou a correção:
php artisan review-schedule:csv-stats data/todos_veiculos.csv
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=3 --dry-run

# Para gerar controladamente por tipo:
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=10 --batch=5
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=car --limit=10 --batch=5
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=hybrid --limit=10 --batch=5
php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=motorcycle --limit=10 --batch=5
```

Agora o sistema deveria funcionar corretamente com limites e filtros! 🎉