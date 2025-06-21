<?php

namespace Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates;

class CarMaintenanceTemplate
{
    // Sistema de controle de variações usadas
    private static array $usedIntros = [];
    private static array $usedConclusions = [];
    private static array $usedMaintenanceStyles = [];
    private static array $usedFAQStyles = [];

    // 25 variações de introdução
    private array $intros = [
        // Grupo 1: Técnicas e profissionais
        "Manter o seu {make} {model} {year} em dia com as revisões é fundamental para garantir sua durabilidade, segurança e bom funcionamento. Seguir o cronograma recomendado pela montadora evita desgastes prematuros e preserva o valor do seu veículo.",
        "O {make} {model} {year} precisa de cuidados periódicos para continuar rodando com segurança e eficiência. Conhecer o cronograma de revisões ajuda a planejar a manutenção e evitar surpresas desagradáveis.",
        "Para garantir a longevidade do seu {make} {model} {year}, é essencial seguir o cronograma de revisões estabelecido pela fabricante. A manutenção preventiva adequada protege seu investimento e evita problemas futuros.",
        "A engenharia do {make} {model} {year} foi desenvolvida para oferecer máxima confiabilidade, mas isso depende do cumprimento rigoroso do cronograma de manutenção. Cada revisão tem um propósito específico na preservação dos sistemas.",
        "Proprietários conscientes do {make} {model} {year} entendem que a manutenção preventiva é a chave para um relacionamento duradouro com o veículo. O cronograma de revisões é o roteiro para essa parceria de sucesso.",

        // Grupo 2: Econômicas e práticas
        "Economizar com a manutenção do {make} {model} {year} começa com o planejamento adequado das revisões. Seguir o cronograma preventivo evita gastos desnecessários com reparos emergenciais e mantém a garantia em dia.",
        "Investir na manutenção preventiva do seu {make} {model} {year} é a melhor forma de evitar custos elevados no futuro. O cronograma de revisões foi desenvolvido para maximizar a vida útil de cada componente.",
        "O custo-benefício da manutenção programada do {make} {model} {year} fica evidente quando comparamos os valores das revisões com os gastos de reparos por negligência. Prevenir sempre sai mais barato que remediar.",
        "Ter um {make} {model} {year} rodando perfeitamente não é sorte, é resultado de planejamento. O cronograma de revisões é seu aliado para manter os custos operacionais sob controle.",
        "A matemática da manutenção do {make} {model} {year} é simples: pequenos investimentos regulares evitam grandes gastos inesperados. O cronograma de revisões é a fórmula para essa economia.",

        // Grupo 3: Segurança e confiabilidade
        "A segurança do {make} {model} {year} depende diretamente do cumprimento do cronograma de revisões. Sistemas como freios, suspensão e direção exigem verificações periódicas para funcionamento adequado.",
        "Dirigir com tranquilidade no seu {make} {model} {year} significa manter todos os sistemas em perfeito estado. O cronograma de revisões garante que componentes críticos sejam verificados regularmente.",
        "A confiabilidade do {make} {model} {year} nas estradas depende de uma manutenção consistente. Seguir as recomendações da montadora é essencial para evitar falhas inesperadas.",
        "Viajar com a família no {make} {model} {year} exige a certeza de que tudo funciona perfeitamente. O cronograma de revisões é sua garantia de viagens seguras e tranquilas.",
        "A responsabilidade de manter o {make} {model} {year} seguro vai além do proprietário, impactando todos que compartilham as vias. O cronograma de revisões é essencial para essa responsabilidade social.",

        // Grupo 4: Específicas por marca premium
        "A tradição de qualidade da {make} no {model} {year} se mantém através de manutenção adequada. O cronograma de revisões foi desenvolvido para preservar as características que tornaram esta marca reconhecida mundialmente.",
        "O {make} {model} {year} representa o melhor da engenharia automotiva, mas depende de manutenção apropriada para manter seu desempenho. Seguir o cronograma é honrar o investimento feito.",
        "Escolher um {make} {model} {year} demonstra bom gosto automotivo. Manter essa escolha através de revisões adequadas garante que o veículo continue correspondendo às expectativas.",
        "A sofisticação do {make} {model} {year} exige cuidados à altura de sua engenharia. O cronograma de revisões preserva a experiência premium que motivou sua escolha.",

        // Grupo 5: Modernas e tecnológicas
        "A tecnologia avançada presente no {make} {model} {year} exige cuidados específicos descritos no cronograma de revisões. Sistemas eletrônicos modernos demandam verificações especializadas para funcionamento otimizado.",
        "O {make} {model} {year} incorpora inovações que melhoram a experiência de condução, mas essas tecnologias precisam de manutenção adequada. O cronograma preventivo preserva todos esses benefícios.",
        "Veículos modernos como o {make} {model} {year} são mais eficientes e duráveis que nunca, mas isso só se mantém com manutenção apropriada. O cronograma de revisões é fundamental nesse processo.",

        // Grupo 6: Valor e patrimônio
        "O {make} {model} {year} é um patrimônio que merece cuidados especiais. Manter o cronograma de revisões em dia não apenas preserva sua funcionalidade, mas também protege seu valor de revenda.",
        "Proteger o investimento feito no {make} {model} {year} significa seguir rigorosamente o cronograma de manutenção. Veículos bem cuidados mantêm valor e desempenho por muito mais tempo.",
        "A depreciação natural do {make} {model} {year} pode ser minimizada através de manutenção adequada. Seguir o cronograma de revisões demonstra cuidado que se reflete no valor do veículo.",

        // Grupo 7: Experiência do usuário
        "Proprietários experientes do {make} {model} {year} sabem que seguir o cronograma de revisões é um investimento inteligente. A manutenção preventiva adequada resulta em menor consumo e maior durabilidade.",
        "A satisfação de dirigir um {make} {model} {year} se mantém ao longo dos anos quando o cronograma de manutenção é respeitado. Cada revisão renova a experiência de condução."
    ];

    // 20 variações de conclusão
    private array $conclusions = [
        // Grupo 1: Técnicas e completas
        "Seguir o cronograma de revisões do {make} {model} {year} é essencial não apenas para manter a garantia, mas principalmente para garantir a segurança, o bom funcionamento e a durabilidade do veículo. As revisões programadas permitem identificar e corrigir problemas antes que se tornem mais graves, além de otimizar o desempenho e a economia de combustível.",
        "A manutenção preventiva do {make} {model} {year} representa um investimento inteligente que se paga ao longo do tempo. Veículos que seguem rigorosamente o cronograma de revisões apresentam menor incidência de falhas, maior vida útil dos componentes e custos operacionais reduzidos.",
        "O cronograma de revisões do {make} {model} {year} foi desenvolvido com base em anos de pesquisa e experiência da montadora. Seguir essas recomendações garante que o veículo opere sempre em condições ideais, proporcionando segurança e economia.",
        "Manter o {make} {model} {year} através do cronograma de revisões é uma decisão que beneficia todos os aspectos da propriedade do veículo. Desde a segurança até a economia, passando pela preservação do valor, cada revisão contribui para uma experiência automotiva superior.",

        // Grupo 2: Econômicas
        "Investir nas revisões programadas do {make} {model} {year} é muito mais econômico que lidar com reparos emergenciais. A manutenção preventiva evita gastos inesperados e mantém o veículo funcionando com eficiência máxima.",
        "O custo das revisões do {make} {model} {year} deve ser visto como um investimento na durabilidade e confiabilidade do veículo. Proprietários que seguem o cronograma relatam menor incidência de problemas e gastos reduzidos com manutenção corretiva.",
        "A economia gerada pela manutenção preventiva do {make} {model} {year} vai além da prevenção de defeitos. Veículos bem mantidos consomem menos combustível, emitem menos poluentes e mantêm melhor valor de revenda.",
        "O retorno do investimento em manutenção preventiva do {make} {model} {year} é imediato e duradouro. Cada real gasto em revisão programa pode economizar vários em reparos emergenciais.",

        // Grupo 3: Segurança
        "A segurança da sua família e de outros usuários da via depende do bom estado do {make} {model} {year}. O cronograma de revisões garante que sistemas críticos como freios, suspensão e direção funcionem adequadamente.",
        "Dirigir um {make} {model} {year} em perfeitas condições proporciona tranquilidade e confiança. As revisões programadas são fundamentais para manter essa segurança e evitar situações de risco na estrada.",
        "A responsabilidade de manter o {make} {model} {year} seguro vai além do proprietário, impactando todos que compartilham as vias. O cronograma de revisões é essencial para essa responsabilidade social.",
        "Cada revisão do {make} {model} {year} é um investimento na segurança de todos que utilizam o veículo. Manter o cronograma em dia é demonstrar cuidado e responsabilidade.",

        // Grupo 4: Experiência e satisfação
        "O {make} {model} {year} foi projetado para oferecer anos de serviço confiável, mas isso só é possível com manutenção adequada. Seguir o cronograma de revisões é a melhor forma de garantir essa longevidade.",
        "Proprietários satisfeitos do {make} {model} {year} têm em comum o cuidado com a manutenção preventiva. O cronograma de revisões é o caminho para manter a satisfação com o veículo ao longo dos anos.",
        "A experiência de dirigir um {make} {model} {year} permanece positiva quando o veículo recebe os cuidados adequados. As revisões programadas são fundamentais para preservar essa experiência.",
        "A relação duradoura entre proprietário e {make} {model} {year} se constrói através da manutenção cuidadosa. Cada revisão fortalece essa parceria e renova o prazer de dirigir.",

        // Grupo 5: Valor e patrimônio
        "Proteger o valor do {make} {model} {year} é tão importante quanto garantir seu funcionamento. O cronograma de revisões contribui para ambos os objetivos, mantendo o veículo em estado premium.",
        "A preservação das características originais do {make} {model} {year} depende do cumprimento do cronograma de manutenção. Cada revisão mantém o veículo próximo às condições de fábrica.",
        "O {make} {model} {year} bem mantido é sinônimo de bom investimento. O cronograma de revisões é a ferramenta para maximizar tanto o desempenho quanto o valor patrimonial.",

        // Grupo 6: Modernas e tecnológicas
        "As tecnologias presentes no {make} {model} {year} foram desenvolvidas para durabilidade, mas dependem de manutenção especializada. O cronograma de revisões garante que todos os sistemas modernos funcionem conforme projetado.",
        "A complexidade dos sistemas do {make} {model} {year} torna o cronograma de revisões ainda mais importante. Manter a tecnologia funcionando perfeitamente exige cuidados programados e especializados."
    ];

    // Variações de estilo para cronogramas
    private array $maintenanceStyles = [
        'tecnico' => [
            'oil_change' => 'Substituição do óleo lubrificante do motor e elemento filtrante',
            'brake_check' => 'Inspeção do sistema de frenagem e verificação de desgaste',
            'fluid_check' => 'Verificação de níveis de todos os fluidos operacionais',
            'cooling_check' => 'Análise do sistema de arrefecimento do motor',
            'air_filter' => 'Inspeção e eventual substituição do filtro de ar',
            'electrical' => 'Diagnóstico eletrônico e verificação do sistema elétrico'
        ],
        'simples' => [
            'oil_change' => 'Troca de óleo e filtro do motor',
            'brake_check' => 'Verificação dos freios',
            'fluid_check' => 'Conferência dos níveis de fluidos',
            'cooling_check' => 'Verificação do sistema de refrigeração',
            'air_filter' => 'Filtro de ar',
            'electrical' => 'Sistema elétrico e diagnóstico'
        ],
        'detalhado' => [
            'oil_change' => 'Drenagem completa e reposição do óleo do motor com filtro original',
            'brake_check' => 'Inspeção minuciosa de pastilhas, discos e sistema hidráulico de freios',
            'fluid_check' => 'Verificação criteriosa de níveis e qualidade de todos os fluidos',
            'cooling_check' => 'Análise completa do sistema de arrefecimento incluindo radiador e mangueiras',
            'air_filter' => 'Limpeza ou substituição do elemento filtrante de ar conforme necessidade',
            'electrical' => 'Diagnóstico computadorizado completo dos sistemas eletrônicos embarcados'
        ],
        'pratico' => [
            'oil_change' => 'Óleo e filtro',
            'brake_check' => 'Freios',
            'fluid_check' => 'Fluidos',
            'cooling_check' => 'Refrigeração',
            'air_filter' => 'Filtro de ar',
            'electrical' => 'Parte elétrica',
            'spark_plugs' => 'Velas',
            'steering_check' => 'Direção',
            'suspension_check' => 'Suspensão',
            'ac_filter' => 'Filtro ar-condicionado',
            'brake_fluid_check' => 'Fluido freio',
            'injection_clean' => 'Injeção',
            'clutch_check' => 'Embreagem',
            'electrical_full' => 'Sistema elétrico',
            'exhaust_check' => 'Escapamento',
            'hoses_check' => 'Mangueiras',
            'alignment_check' => 'Alinhamento',
            'fuel_filter' => 'Filtro combustível',
            'brake_fluid_change' => 'Troca fluido freio',
            'belts_check' => 'Correias',
            'transmission_oil' => 'Óleo transmissão',
            'battery_check' => 'Bateria',
            'fuel_system_check' => 'Sistema combustível',
            'cooling_system_full' => 'Sistema arrefecimento',
            'power_steering_check' => 'Direção assistida',
            'cv_joints_check' => 'Juntas homocinéticas',
            'brake_system_full' => 'Sistema freios',
            'engine_mounts' => 'Coxins motor',
            'suspension_full' => 'Suspensão completa',
            'full_revision' => 'Revisão geral',
            'spark_plugs_change' => 'Troca velas',
            'belts_change' => 'Troca correias',
            'timing_belt_check' => 'Correia dentada',
            'filters_change' => 'Troca filtros',
            'transmission_fluids' => 'Fluidos transmissão',
            'emission_control' => 'Controle emissões'
        ]
    ];

    // Base do cronograma (mantém estrutura mas varia descrições)
    private array $maintenanceSchedule = [
        '10.000 km ou 12 meses' => [
            'oil_change',
            'brake_check', 
            'fluid_check',
            'cooling_check',
            'air_filter',
            'electrical'
        ],
        '20.000 km ou 24 meses' => [
            'oil_change',
            'air_filter',
            'spark_plugs',
            'steering_check',
            'suspension_check',
            'ac_filter',
            'brake_fluid_check'
        ],
        '30.000 km ou 36 meses' => [
            'oil_change',
            'injection_clean',
            'clutch_check',
            'electrical_full',
            'exhaust_check',
            'hoses_check',
            'alignment_check'
        ],
        '40.000 km ou 48 meses' => [
            'oil_change',
            'fuel_filter',
            'brake_fluid_change',
            'belts_check',
            'transmission_oil',
            'battery_check',
            'fuel_system_check'
        ],
        '50.000 km ou 60 meses' => [
            'oil_change',
            'cooling_system_full',
            'power_steering_check',
            'cv_joints_check',
            'brake_system_full',
            'engine_mounts',
            'suspension_full'
        ],
        '60.000 km ou 72 meses' => [
            'full_revision',
            'oil_change',
            'spark_plugs_change',
            'belts_change',
            'timing_belt_check',
            'filters_change',
            'transmission_fluids',
            'emission_control'
        ]
    ];

    // Variações de FAQs
    private array $faqVariations = [
        'basico' => [
            'intervalo' => "Qual o intervalo ideal para as revisões do {make} {model}?",
            'oficina' => 'Posso realizar as revisões fora da concessionária sem perder a garantia?',
            'atraso' => 'O que acontece se eu atrasar uma revisão?'
        ],
        'completo' => [
            'intervalo' => "Com que frequência devo levar meu {make} {model} {year} para revisão?",
            'oficina' => 'É obrigatório fazer as revisões na concessionária?',
            'atraso' => 'Posso atrasar uma revisão sem prejuízos?',
            'custo' => "Quanto custa em média uma revisão do {make} {model}?",
            'garantia' => 'Como a manutenção afeta a garantia do veículo?'
        ],
        'pratico' => [
            'intervalo' => "De quanto em quanto tempo devo revisar o {make} {model}?",
            'local' => 'Onde posso fazer as revisões?',
            'necessidade' => 'É realmente necessário seguir o cronograma à risca?',
            'economia' => 'As revisões realmente compensam financeiramente?'
        ]
    ];

    /**
     * Método para limpar estado entre gerações
     */
    public static function clearState(): void
    {
        self::$usedIntros = [];
        self::$usedConclusions = [];
        self::$usedMaintenanceStyles = [];
        self::$usedFAQStyles = [];
    }

    public function generateIntroduction(array $vehicleData): string
    {
        $vehicleKey = $this->getVehicleKey($vehicleData);
        $availableIntros = $this->getAvailableContent($this->intros, self::$usedIntros, $vehicleKey);
        $selectedIntro = $availableIntros[array_rand($availableIntros)];
        $this->markAsUsed(self::$usedIntros, $vehicleKey, $selectedIntro);
        
        return $this->replacePlaceholders($selectedIntro, $vehicleData);
    }

    public function generateOverviewTable(array $vehicleData): array
    {
        $style = $this->selectMaintenanceStyle($vehicleData);
        
        return [
            ['revisao' => '1ª Revisão', 'intervalo' => '10.000 km ou 12 meses', 'principais_servicos' => 'Troca de óleo e filtros, verificações gerais', 'estimativa_custo' => $this->getCostRange($vehicleData, 1)],
            ['revisao' => '2ª Revisão', 'intervalo' => '20.000 km ou 24 meses', 'principais_servicos' => 'Óleo, filtros, velas, verificação completa', 'estimativa_custo' => $this->getCostRange($vehicleData, 2)],
            ['revisao' => '3ª Revisão', 'intervalo' => '30.000 km ou 36 meses', 'principais_servicos' => 'Óleo, filtros, fluido de freio, injeção', 'estimativa_custo' => $this->getCostRange($vehicleData, 3)],
            ['revisao' => '4ª Revisão', 'intervalo' => '40.000 km ou 48 meses', 'principais_servicos' => 'Óleo, filtros, correias, transmissão', 'estimativa_custo' => $this->getCostRange($vehicleData, 4)],
            ['revisao' => '5ª Revisão', 'intervalo' => '50.000 km ou 60 meses', 'principais_servicos' => 'Óleo, arrefecimento, direção, suspensão', 'estimativa_custo' => $this->getCostRange($vehicleData, 5)],
            ['revisao' => '6ª Revisão', 'intervalo' => '60.000 km ou 72 meses', 'principais_servicos' => 'Revisão ampla, correia dentada, fluidos', 'estimativa_custo' => $this->getCostRange($vehicleData, 6)]
        ];
    }

    // ============================================================================
    // CORREÇÃO: generateDetailedSchedule() - GARANTIR EXATAMENTE 6 REVISÕES
    // ============================================================================
    public function generateDetailedSchedule(array $vehicleData): array
    {
        $style = $this->selectMaintenanceStyle($vehicleData);
        $schedule = [];
        
        // GARANTIR EXATAMENTE 6 REVISÕES - INTERVALOS FIXOS
        $revisionData = [
            1 => ['interval' => '10.000 km ou 12 meses', 'km' => '10.000'],
            2 => ['interval' => '20.000 km ou 24 meses', 'km' => '20.000'], 
            3 => ['interval' => '30.000 km ou 36 meses', 'km' => '30.000'],
            4 => ['interval' => '40.000 km ou 48 meses', 'km' => '40.000'],
            5 => ['interval' => '50.000 km ou 60 meses', 'km' => '50.000'],
            6 => ['interval' => '60.000 km ou 72 meses', 'km' => '60.000']
        ];
        
        for ($revisionNumber = 1; $revisionNumber <= 6; $revisionNumber++) {
            // Buscar serviços específicos ou usar padrão
            $serviceKeys = $this->getServicesForRevision($revisionNumber, $vehicleData);
            $services = $this->translateServices($serviceKeys, $style);
            
            $schedule[] = [
                'numero_revisao' => $revisionNumber,
                'intervalo' => $revisionData[$revisionNumber]['interval'],
                'km' => $revisionData[$revisionNumber]['km'],
                'servicos_principais' => array_slice($services, 0, 4),
                'verificacoes_complementares' => array_slice($services, 4),
                'estimativa_custo' => $this->getCostRange($vehicleData, $revisionNumber),
                'observacoes' => $this->getVariedObservation($revisionNumber, $vehicleData)
            ];
        }
        
        return $schedule;
    }

    // ============================================================================
    // NOVO: Método auxiliar para buscar serviços por revisão
    // ============================================================================
    private function getServicesForRevision(int $revision, array $vehicleData): array
    {
        // Tentar buscar do maintenanceSchedule existente primeiro
        $intervalKeys = array_keys($this->maintenanceSchedule ?? []);
        
        if (isset($intervalKeys[$revision - 1])) {
            $key = $intervalKeys[$revision - 1];
            if (isset($this->maintenanceSchedule[$key])) {
                return $this->maintenanceSchedule[$key];
            }
        }
        
        // Fallback: serviços padrão baseados no número da revisão
        return $this->getDefaultServicesForRevision($revision, $vehicleData);
    }

    // ============================================================================
    // NOVO: Método auxiliar para serviços padrão
    // ============================================================================
    private function getDefaultServicesForRevision(int $revision, array $vehicleData): array
    {
        // Serviços padrão para carros convencionais
        switch ($revision) {
            case 1: return ['oil_change', 'brake_check', 'fluid_check', 'cooling_check', 'air_filter', 'electrical'];
            case 2: return ['oil_change', 'air_filter', 'spark_plugs', 'steering_check', 'suspension_check', 'ac_filter'];
            case 3: return ['oil_change', 'injection_clean', 'clutch_check', 'electrical_full', 'exhaust_check', 'hoses_check'];
            case 4: return ['oil_change', 'fuel_filter', 'brake_fluid_change', 'belts_check', 'transmission_oil', 'battery_check'];
            case 5: return ['oil_change', 'cooling_system_full', 'power_steering_check', 'cv_joints_check', 'brake_system_full', 'engine_mounts'];
            case 6: return ['full_revision', 'oil_change', 'spark_plugs_change', 'belts_change', 'timing_belt_check', 'filters_change'];
            default: return ['oil_change', 'brake_check', 'fluid_check'];
        }
    }

    public function generatePreventiveMaintenance(array $vehicleData): array
    {
        $style = $this->selectMaintenanceStyle($vehicleData);
        
        $variations = [
            'simples' => [
                'verificacoes_mensais' => [
                    'Verificar nível do óleo do motor',
                    'Conferir água do radiador', 
                    'Calibrar pneus',
                    'Testar luzes'
                ],
                'verificacoes_trimestrais' => [
                    'Fluido de freio',
                    'Bateria e terminais',
                    'Desgaste dos pneus',
                    'Limpador de para-brisa'
                ]
            ],
            'detalhado' => [
                'verificacoes_mensais' => [
                    'Monitoramento do nível e viscosidade do óleo lubrificante',
                    'Verificação da temperatura e nível do sistema de arrefecimento',
                    'Manutenção da pressão adequada em todos os pneumáticos',
                    'Inspeção completa do sistema de iluminação'
                ],
                'verificacoes_trimestrais' => [
                    'Análise do nível e qualidade do fluido de freio',
                    'Verificação da bateria, terminais e sistema de carga',
                    'Inspeção detalhada do desgaste e alinhamento dos pneus',
                    'Manutenção do sistema limpador e fluidos'
                ]
            ]
        ];

        $selectedStyle = in_array($style, ['tecnico', 'detalhado']) ? 'detalhado' : 'simples';
        
        return array_merge($variations[$selectedStyle], [
            'verificacoes_anuais' => [
                'Sistema de ar-condicionado completo',
                'Correias e componentes do motor',
                'Suspensão e componentes da direção',
                'Sistema de escapamento',
                'Lubrificação geral de articulações'
            ]
        ]);
    }
    

    public function generateCriticalParts(array $vehicleData): array
    {
        $parts = [
            [
                'componente' => 'Correia Dentada',
                'intervalo_recomendado' => '60.000 km ou 4 anos',
                'observacao' => 'A ruptura da correia dentada pode causar danos graves ao motor'
            ],
            [
                'componente' => 'Fluido de Freio',
                'intervalo_recomendado' => '30.000 km ou 2 anos', 
                'observacao' => 'O fluido de freio absorve umidade e perde eficiência com o tempo'
            ],
            [
                'componente' => 'Filtro de Combustível',
                'intervalo_recomendado' => '20.000 km',
                'observacao' => 'Um filtro obstruído causa perda de potência e aumento no consumo'
            ]
        ];

        // Adicionar peças específicas por tipo de veículo
        $category = strtolower($vehicleData['category'] ?? '');
        if (in_array($category, ['suv', 'pickup'])) {
            $parts[] = [
                'componente' => 'Óleo do Diferencial',
                'intervalo_recomendado' => '40.000 km',
                'observacao' => 'Essencial para veículos com tração 4x4 ou uso pesado'
            ];
        }

        return $parts;
    }

    public function generateTechnicalSpecs(array $vehicleData): array
    {
        return [
            'capacidade_oleo' => $this->getOilCapacity($vehicleData),
            'tipo_oleo_recomendado' => $this->getRecommendedOil($vehicleData),
            'intervalo_troca_oleo' => '10.000 km ou 12 meses',
            'filtro_oleo_original' => $this->getOriginalFilter($vehicleData),
            'capacidade_combustivel' => $this->getFuelCapacity($vehicleData),
            'fluido_freio' => 'DOT 4',
            'pressao_pneus' => $this->getTirePressure($vehicleData)
        ];
    }

    public function generateWarrantyInfo(array $vehicleData): array
    {
        $year = $vehicleData['year'] ?? date('Y');
        $isNew = ($year >= (date('Y') - 2));
        
        return [
            'prazo_garantia' => $isNew ? '3 anos ou 100.000 km' : '1 ano da concessionária',
            'garantia_anticorrosao' => '6 anos',
            'garantia_itens_desgaste' => '1 ano ou 20.000 km',
            'observacoes_importantes' => 'Para manter a garantia válida, todas as revisões devem ser realizadas dentro do prazo estipulado, com uma tolerância máxima de 1.000 km ou 30 dias.',
            'dicas_vida_util' => $this->getLifeTips($vehicleData)
        ];
    }

    public function generateFAQs(array $vehicleData): array
    {
        $vehicleKey = $this->getVehicleKey($vehicleData);
        $style = $this->getFAQStyle($vehicleKey);
        
        $questions = $this->faqVariations[$style];
        $faqs = [];
        
        foreach ($questions as $key => $question) {
            $faqs[] = [
                'pergunta' => $this->replacePlaceholders($question, $vehicleData),
                'resposta' => $this->getFAQAnswer($key, $vehicleData, $style)
            ];
        }
        
        return $faqs;
    }

    public function generateConclusion(array $vehicleData): string
    {
        $vehicleKey = $this->getVehicleKey($vehicleData);
        $availableConclusions = $this->getAvailableContent($this->conclusions, self::$usedConclusions, $vehicleKey);
        $selectedConclusion = $availableConclusions[array_rand($availableConclusions)];
        $this->markAsUsed(self::$usedConclusions, $vehicleKey, $selectedConclusion);
        
        return $this->replacePlaceholders($selectedConclusion, $vehicleData);
    }

    // Métodos auxiliares privados

    private function getVehicleKey(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');
        $year = $vehicleData['year'] ?? date('Y');
        
        // Agrupar por faixas de ano e características similares
        $yearGroup = floor($year / 3) * 3; // Grupos de 3 anos
        $segment = $this->getVehicleSegment($vehicleData);
        
        return "{$make}_{$segment}_{$yearGroup}";
    }

    private function getVehicleSegment(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');
        
        // Classificar por segmento
        if (in_array($make, ['bmw', 'mercedes-benz', 'audi', 'volvo', 'jaguar'])) {
            return 'premium';
        } elseif (in_array($category, ['pickup', 'suv'])) {
            return 'utilitario'; 
        } elseif (in_array($category, ['hatch', 'sedan'])) {
            return 'popular';
        }
        
        return 'geral';
    }

    private function selectMaintenanceStyle(array $vehicleData): string
    {
        $segment = $this->getVehicleSegment($vehicleData);
        $vehicleKey = $this->getVehicleKey($vehicleData);
        
        // Alternar estilos para evitar repetição
        $usedStyles = self::$usedMaintenanceStyles[$vehicleKey] ?? [];
        $availableStyles = array_diff(array_keys($this->maintenanceStyles), $usedStyles);
        
        if (empty($availableStyles)) {
            self::$usedMaintenanceStyles[$vehicleKey] = [];
            $availableStyles = array_keys($this->maintenanceStyles);
        }
        
        // CORREÇÃO: Verificação adicional para garantir que availableStyles não está vazio
        if (empty($availableStyles)) {
            $availableStyles = ['tecnico']; // Fallback seguro
        }
        
        // Preferência por segmento
        $preferredStyles = [
            'premium' => ['tecnico', 'detalhado'],
            'popular' => ['simples', 'pratico'],
            'utilitario' => ['detalhado', 'tecnico']
        ];
        
        $preferred = $preferredStyles[$segment] ?? ['tecnico'];
        $intersection = array_intersect($preferred, $availableStyles);
        
        // CORREÇÃO: Validação mais robusta para evitar array vazio
        if (empty($intersection)) {
            $selectedStyle = $availableStyles[0];
        } else {
            $selectedStyle = $intersection[array_rand($intersection)];
        }
        
        // Marcar como usado
        if (!isset(self::$usedMaintenanceStyles[$vehicleKey])) {
            self::$usedMaintenanceStyles[$vehicleKey] = [];
        }
        self::$usedMaintenanceStyles[$vehicleKey][] = $selectedStyle;
        
        return $selectedStyle;
    }

    private function translateServices(array $serviceKeys, string $style): array
    {
        // CORREÇÃO: Garantir que o estilo existe
        $styleData = $this->maintenanceStyles[$style] ?? $this->maintenanceStyles['tecnico'];
        $services = [];
        
        foreach ($serviceKeys as $key) {
            if (isset($styleData[$key])) {
                $services[] = $styleData[$key];
            } else {
                // Fallback para serviços não mapeados
                $services[] = $this->getServiceFallback($key);
            }
        }
        
        return $services;
    }

    private function getServiceFallback(string $serviceKey): string
    {
        $fallbacks = [
            'spark_plugs' => 'Verificação das velas de ignição',
            'steering_check' => 'Inspeção do sistema de direção',
            'suspension_check' => 'Verificação das suspensões',
            'ac_filter' => 'Troca do filtro de ar-condicionado',
            'brake_fluid_check' => 'Verificação do fluido de freio',
            'injection_clean' => 'Limpeza do sistema de injeção',
            'clutch_check' => 'Inspeção do sistema de embreagem',
            'electrical_full' => 'Verificação completa do sistema elétrico',
            'exhaust_check' => 'Inspeção do sistema de escapamento',
            'hoses_check' => 'Verificação de mangueiras e tubulações',
            'alignment_check' => 'Verificação do alinhamento',
            'fuel_filter' => 'Substituição do filtro de combustível',
            'brake_fluid_change' => 'Troca do fluido de freio',
            'belts_check' => 'Verificação das correias',
            'transmission_oil' => 'Verificação do óleo da transmissão',
            'battery_check' => 'Verificação da bateria',
            'fuel_system_check' => 'Inspeção do sistema de alimentação',
            'cooling_system_full' => 'Verificação completa do arrefecimento',
            'power_steering_check' => 'Verificação da direção assistida',
            'cv_joints_check' => 'Inspeção das juntas homocinéticas',
            'brake_system_full' => 'Verificação completa do sistema de freios',
            'engine_mounts' => 'Verificação dos coxins do motor',
            'suspension_full' => 'Inspeção completa da suspensão',
            'full_revision' => 'Revisão ampla de todos os sistemas',
            'spark_plugs_change' => 'Substituição das velas de ignição',
            'belts_change' => 'Substituição de correias auxiliares',
            'timing_belt_check' => 'Verificação da correia dentada',
            'filters_change' => 'Troca de filtros diversos',
            'transmission_fluids' => 'Troca de fluidos da transmissão',
            'emission_control' => 'Verificação do controle de emissões'
        ];
        
        // CORREÇÃO: Fallback final para evitar erros
        return $fallbacks[$serviceKey] ?? 'Verificação não especificada';
    }

    private function getCostRange(array $vehicleData, int $revisionNumber): string
    {
        $segment = $this->getVehicleSegment($vehicleData);
        $year = $vehicleData['year'] ?? date('Y');
        
        // Custos base por revisão
        $baseCosts = [
            1 => [350, 450],
            2 => [520, 620], 
            3 => [640, 750],
            4 => [830, 950],
            5 => [540, 650],
            6 => [1050, 1300]
        ];
        
        $base = $baseCosts[$revisionNumber] ?? [400, 500];
        
        // Ajustar por segmento
        $multipliers = [
            'premium' => 1.4,
            'utilitario' => 1.2,
            'popular' => 0.9,
            'geral' => 1.0
        ];
        
        $multiplier = $multipliers[$segment] ?? 1.0;
        
        // Ajustar por idade (veículos mais antigos custam menos)
        $currentYear = date('Y');
        if (($currentYear - $year) > 5) {
            $multiplier *= 0.85;
        }
        
        $min = round($base[0] * $multiplier);
        $max = round($base[1] * $multiplier);
        
        return "R$ {$min} - R$ {$max}";
    }

    private function getVariedObservation(int $revisionNumber, array $vehicleData): string
    {
        $observations = [
            1 => [
                'A primeira revisão é essencial para manter a garantia do veículo e verificar a adaptação dos componentes.',
                'Esta revisão inicial confirma o correto assentamento de todas as peças após o período de amaciamento.',
                'Fundamental para validar a garantia e detectar qualquer anomalia no funcionamento inicial.',
                'Revisão de adaptação que verifica se todos os sistemas estão funcionando conforme especificado.',
                'Primeira verificação completa após o período inicial de uso do veículo.'
            ],
            3 => [
                'Revisão importante que inclui verificação de componentes com desgaste médio prazo.',
                'Momento ideal para prevenção de problemas e renovação de fluidos essenciais.',
                'Esta revisão foca na manutenção de sistemas que começam a mostrar sinais de uso.',
                'Verificação crucial para manter a confiabilidade do veículo a médio prazo.'
            ],
            6 => [
                'Esta revisão inclui a correia dentada, componente crítico para o funcionamento do motor.',
                'Revisão ampla que renova componentes essenciais e avalia o estado geral do veículo.',
                'Momento importante para substituição de itens críticos e verificação completa.',
                'Revisão de renovação que prepara o veículo para mais quilômetros de uso confiável.',
                'Verificação completa que inclui componentes de maior durabilidade e importância.'
            ]
        ];
        
        $defaultObs = [
            'Revisão importante para manter o bom funcionamento do veículo.',
            'Verificação essencial para preservar a vida útil dos componentes.',
            'Manutenção preventiva para garantir segurança e economia.',
            'Inspeção programada para detectar e prevenir problemas futuros.'
        ];
        
        $availableObs = $observations[$revisionNumber] ?? $defaultObs;
        return $availableObs[array_rand($availableObs)];
    }

    private function getFAQStyle(string $vehicleKey): string
    {
        $usedStyles = self::$usedFAQStyles[$vehicleKey] ?? [];
        $availableStyles = array_diff(['basico', 'completo', 'pratico'], $usedStyles);
        
        if (empty($availableStyles)) {
            self::$usedFAQStyles[$vehicleKey] = [];
            $availableStyles = ['basico', 'completo', 'pratico'];
        }
        
        $selectedStyle = $availableStyles[array_rand($availableStyles)];
        
        if (!isset(self::$usedFAQStyles[$vehicleKey])) {
            self::$usedFAQStyles[$vehicleKey] = [];
        }
        self::$usedFAQStyles[$vehicleKey][] = $selectedStyle;
        
        return $selectedStyle;
    }

    private function getFAQAnswer(string $questionKey, array $vehicleData, string $style): string
    {
        $make = $vehicleData['make'] ?? '';
        $model = $vehicleData['model'] ?? '';
        
        $answers = [
            'intervalo' => [
                'basico' => 'A maioria dos fabricantes recomenda revisões a cada 10.000 km ou 12 meses, o que ocorrer primeiro.',
                'completo' => 'Para veículos modernos, o intervalo padrão é de 10.000 km ou 12 meses. Veículos com uso severo podem necessitar intervalos menores.',
                'pratico' => 'Geralmente a cada 10.000 km ou 1 ano. Consulte sempre o manual do proprietário para confirmar.'
            ],
            'oficina' => [
                'basico' => 'Sim, desde que a oficina siga os procedimentos do manual e utilize peças originais ou homologadas.',
                'completo' => 'A garantia não exige revisões na concessionária, mas é importante escolher oficinas qualificadas que sigam as especificações do fabricante.',
                'pratico' => 'Pode fazer em qualquer oficina autorizada, desde que use peças adequadas e registre tudo.'
            ],
            'atraso' => [
                'basico' => 'Atrasos podem comprometer a garantia e permitir o desenvolvimento de problemas evitáveis.',
                'completo' => 'Pequenos atrasos são tolerados (até 1.000 km ou 30 dias), mas atrasos maiores podem cancelar a garantia.',
                'pratico' => 'Melhor não atrasar. Se acontecer, procure fazer o quanto antes para não perder a garantia.'
            ],
            'local' => 'Concessionárias, oficinas autorizadas ou independentes qualificadas podem realizar as revisões.',
            'necessidade' => 'Sim, seguir o cronograma evita problemas maiores e mantém a garantia válida.',
            'economia' => 'Definitivamente. A manutenção preventiva custa muito menos que reparos emergenciais.',
            'custo' => "Os custos variam conforme a revisão, mas geralmente ficam entre R$ 400 e R$ 1.200 para veículos como o {$make} {$model}.",
            'garantia' => 'Seguir o cronograma de revisões é essencial para manter a garantia de fábrica válida.'
        ];
        
        if (isset($answers[$questionKey])) {
            if (is_array($answers[$questionKey])) {
                return $answers[$questionKey][$style] ?? $answers[$questionKey]['basico'];
            }
            return $answers[$questionKey];
        }
        
        return 'Consulte o manual do proprietário ou uma oficina especializada para informações específicas.';
    }

    private function getLifeTips(array $vehicleData): array
    {
        $segment = $this->getVehicleSegment($vehicleData);
        $category = strtolower($vehicleData['category'] ?? '');
        
        $baseTips = [
            'Evite acelerações bruscas, especialmente com o motor frio',
            'Realize a troca de óleo no intervalo correto',
            'Mantenha os pneus calibrados conforme recomendação',
            'Use combustível de qualidade',
            'Não ignore ruídos ou comportamentos estranhos'
        ];
        
        // Adicionar dicas específicas por segmento
        if ($segment === 'premium') {
            $baseTips[] = 'Utilize sempre peças originais ou homologadas';
            $baseTips[] = 'Procure oficinas especializadas na marca';
        } elseif ($category === 'pickup' || $category === 'suv') {
            $baseTips[] = 'Verifique regularmente o óleo do diferencial';
            $baseTips[] = 'Inspecione a suspensão após uso em terrenos difíceis';
        }
        
        return $baseTips;
    }

    private function getAvailableContent(array $content, array &$used, string $vehicleKey): array
    {
        $usedForVehicle = $used[$vehicleKey] ?? [];
        $available = array_diff($content, $usedForVehicle);
        
        // Se todos já foram usados, resetar
        if (empty($available)) {
            $used[$vehicleKey] = [];
            $available = $content;
        }
        
        return array_values($available);
    }

    private function markAsUsed(array &$usedArray, string $vehicleKey, string $content): void
    {
        if (!isset($usedArray[$vehicleKey])) {
            $usedArray[$vehicleKey] = [];
        }
        $usedArray[$vehicleKey][] = $content;
    }

    private function replacePlaceholders(string $text, array $vehicleData): string
    {
        return str_replace(
            ['{make}', '{model}', '{year}'],
            [$vehicleData['make'] ?? '', $vehicleData['model'] ?? '', $vehicleData['year'] ?? ''],
            $text
        );
    }

    private function getOilCapacity(array $vehicleData): string
    {
        $model = strtolower($vehicleData['model'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');
        
        // Capacidades baseadas no tipo/modelo
        if (strpos($model, 'mobi') !== false || strpos($model, 'up') !== false) return '3.2 litros';
        if (strpos($model, 'uno') !== false || strpos($model, 'ka') !== false) return '3.5 litros';
        if (in_array($category, ['pickup', 'suv'])) return '5.5 litros';
        if (strpos($model, 'civic') !== false || strpos($model, 'corolla') !== false) return '4.2 litros';
        
        return '4.0 litros';
    }

    private function getRecommendedOil(array $vehicleData): string
    {
        $recommendedOil = $vehicleData['recommended_oil'] ?? '';
        if (!empty($recommendedOil) && $recommendedOil !== 'NA') {
            return $recommendedOil;
        }
        
        $year = $vehicleData['year'] ?? date('Y');
        $segment = $this->getVehicleSegment($vehicleData);
        
        // Veículos mais novos usam óleos de menor viscosidade
        if ($year >= 2020) {
            return $segment === 'premium' ? '0W20 Sintético' : '5W30 Sintético';
        } elseif ($year >= 2015) {
            return '5W30 Semissintético';
        } else {
            return '10W40 Semissintético';
        }
    }

    private function getOriginalFilter(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        
        $filters = [
            'honda' => '15400-RTA-003',
            'toyota' => '90915-YZZD4',
            'fiat' => '55256470',
            'chevrolet' => '93185674',
            'volkswagen' => '032115561A',
            'ford' => '1S7G-6714-AA',
            'nissan' => '15208-65F0A',
            'hyundai' => '26300-35504'
        ];
        
        return $filters[$make] ?? 'Consulte manual do proprietário';
    }

    private function getFuelCapacity(array $vehicleData): string
    {
        $model = strtolower($vehicleData['model'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');
        
        if (strpos($model, 'mobi') !== false || strpos($model, 'up') !== false) return '50 litros';
        if (in_array($category, ['pickup'])) return '80 litros';
        if (in_array($category, ['suv'])) return '65 litros';
        if (strpos($model, 'civic') !== false) return '47 litros';
        if (strpos($model, 'corolla') !== false) return '50 litros';
        
        return '55 litros';
    }

    private function getTirePressure(array $vehicleData): string
    {
        $emptyFront = $vehicleData['pressure_empty_front'] ?? 32;
        $emptyRear = $vehicleData['pressure_empty_rear'] ?? 30;
        
        return "Dianteiros: {$emptyFront} PSI | Traseiros: {$emptyRear} PSI (veículo vazio)";
    }

    private function estimateCost(string $km): string
    {
        // Método mantido para compatibilidade - usa getCostRange internamente
        $kmInt = (int)str_replace('.', '', $km);
        
        return match (true) {
            $kmInt <= 10000 => 'R$ 390 - R$ 450',
            $kmInt <= 20000 => 'R$ 560 - R$ 620', 
            $kmInt <= 30000 => 'R$ 680 - R$ 750',
            $kmInt <= 40000 => 'R$ 870 - R$ 950',
            $kmInt <= 50000 => 'R$ 580 - R$ 650',
            default => 'R$ 1.100 - R$ 1.300'
        };
    }
}