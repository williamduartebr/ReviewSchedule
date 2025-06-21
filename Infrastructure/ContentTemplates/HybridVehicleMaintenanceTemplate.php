<?php

namespace Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates;

class HybridVehicleMaintenanceTemplate
{
    // Sistema de controle de variações usadas
    private static array $usedIntros = [];
    private static array $usedConclusions = [];
    private static array $usedMaintenanceStyles = [];
    private static array $usedFAQStyles = [];

    // 25 variações de introdução específicas para veículos híbridos
    private array $intros = [
        // Grupo 1: Tecnologia dual e complexidade
        "O seu {make} {model} {year} híbrido combina sistemas de propulsão convencional e elétrica, exigindo um cronograma de revisões específico. Manter ambos os sistemas em perfeito funcionamento garante a eficiência de consumo e durabilidade.",
        "Veículos híbridos como o {make} {model} {year} possuem sistemas complexos que integram motor a combustão e elétrico. O cronograma de revisões foi especialmente desenvolvido para atender às necessidades dessa tecnologia avançada.",
        "O {make} {model} {year} híbrido apresenta vantagens significativas de economia, mas depende de manutenção apropriada para manter sua eficiência. Seguir o cronograma de revisões garantirá o funcionamento ideal de todos os sistemas.",
        "A engenharia sofisticada do {make} {model} {year} híbrido integra duas formas de propulsão em harmonia perfeita. O cronograma de manutenção foi desenvolvido para preservar essa complexa sinergia entre sistemas.",
        "Manter um {make} {model} {year} híbrido funcionando perfeitamente requer conhecimento especializado sobre a interação entre motor elétrico e a combustão. O cronograma de revisões garante essa integração ideal.",

        // Grupo 2: Economia e eficiência
        "A economia de combustível excepcional do {make} {model} {year} híbrido se mantém através de manutenção adequada de ambos os sistemas de propulsão. O cronograma preventivo preserva essa vantagem econômica única.",
        "Investir na manutenção preventiva do seu {make} {model} {year} híbrido protege a tecnologia que proporciona economia superior. O cronograma adequado maximiza os benefícios da hibridização.",
        "O {make} {model} {year} híbrido representa o equilíbrio perfeito entre economia e desempenho. Manter esse equilíbrio exige seguir rigorosamente o cronograma de revisões especializado.",
        "Proprietários conscientes do {make} {model} {year} híbrido entendem que a manutenção adequada multiplica a economia de combustível e reduz a pegada ambiental do veículo.",
        "A revolução na eficiência energética do {make} {model} {year} híbrido depende de cuidados específicos que preservem tanto o motor quanto o sistema elétrico funcionando em harmonia.",

        // Grupo 3: Performance e tecnologia
        "A performance inteligente do {make} {model} {year} híbrido alterna automaticamente entre propulsões para máxima eficiência. O cronograma de revisões mantém essa tecnologia funcionando perfeitamente.",
        "O {make} {model} {year} híbrido oferece a potência quando necessária e economia quando possível. Preservar essas características exige manutenção especializada de ambos os sistemas.",
        "A tecnologia avançada do {make} {model} {year} híbrido gerencia inteligentemente energia entre motor elétrico e combustão. O cronograma de manutenção preserva essa sofisticação tecnológica.",
        "Para garantir que seu {make} {model} {year} híbrido continue entregando performance otimizada, é essencial seguir o cronograma que considera as particularidades dos sistemas integrados.",
        "A eficiência inteligente do {make} {model} {year} híbrido está diretamente ligada ao estado de conservação de seus múltiplos sistemas. O cronograma de revisões otimiza essa integração.",

        // Grupo 4: Sustentabilidade e responsabilidade
        "Escolher um {make} {model} {year} híbrido demonstra consciência ambiental que se estende à manutenção responsável. O cronograma de revisões mantém a pegada ecológica reduzida.",
        "A sustentabilidade do {make} {model} {year} híbrido depende não apenas de sua operação eficiente, but também de manutenção adequada que preserve essas características verdes.",
        "Manter o {make} {model} {year} híbrido adequadamente é contribuir para um futuro mais sustentável, preservando a tecnologia que representa a transição ecológica da mobilidade.",
        "O {make} {model} {year} híbrido simboliza a evolução responsável da indústria automotiva. A manutenção adequada honra essa contribuição ambiental.",

        // Grupo 5: Complexidade e especialização
        "A sofisticação técnica do {make} {model} {year} híbrido exige cuidados especializados que só profissionais qualificados podem oferecer. O cronograma garante essas verificações complexas.",
        "Manter a harmonia entre os sistemas do {make} {model} {year} híbrido requer conhecimento específico sobre tecnologias de hibridização. As revisões programadas preservam essa integração.",
        "A tecnologia híbrida do {make} {model} {year} representa anos de desenvolvimento em eficiência energética. A manutenção adequada preserva essa evolução tecnológica.",
        "O {make} {model} {year} híbrido combina décadas de pesquisa em propulsão alternativa. O cronograma de manutenção protege esse avanço da engenharia automotiva.",

        // Grupo 6: Experiência e conveniência
        "A experiência refinada de dirigir um {make} {model} {year} híbrido se mantém através de cuidados especializados com seus sistemas integrados e interdependentes.",
        "Proprietários do {make} {model} {year} híbrido desfrutam de operação silenciosa e econômica. As revisões programadas garantem que essas características sejam preservadas.",
        "O {make} {model} {year} híbrido oferece a conveniência de menor consumo sem abrir mão da performance. O cronograma de manutenção preserva esse benefício dual.",
        "A revolução na experiência automotiva do {make} {model} {year} híbrido inclui um cronograma de manutenção otimizado para tecnologias de nova geração."
    ];

    // 20 variações de conclusão específicas para híbridos
    private array $conclusions = [
        // Grupo 1: Tecnologia e integração
        "A tecnologia híbrida do {make} {model} {year} combina o melhor dos mundos a combustão e elétrico, mas exige manutenção adequada para manter esta integração funcionando perfeitamente. Seguir o cronograma de revisões garante não apenas segurança, mas também a preservação da economia de combustível característica deste veículo avançado.",
        "A manutenção do {make} {model} {year} híbrido representa uma abordagem moderna de cuidados automotivos, considerando tanto sistemas convencionais quanto elétricos. Cada revisão programada contribui para manter a harmonia entre essas tecnologias.",
        "O cronograma de revisões do {make} {model} {year} híbrido foi desenvolvido para atender às necessidades específicas da propulsão dual. Seguir essas recomendações preserva a eficiência e longevidade de ambos os sistemas.",
        "Manter o {make} {model} {year} híbrido através do cronograma adequado é investir na tecnologia do futuro, preservando tanto a performance quanto a economia que motivaram sua escolha.",

        // Grupo 2: Economia e valor
        "O investimento em manutenção preventiva do {make} {model} {year} híbrido se paga através de economia de combustível mantida, maior durabilidade dos componentes e custos operacionais otimizados.",
        "A economia operacional do {make} {model} {year} híbrido se estende por toda sua vida útil quando o cronograma de manutenção especializado é respeitado rigorosamente.",
        "Proteger o valor do {make} {model} {year} híbrido significa preservar tanto sua tecnologia avançada quanto suas vantagens econômicas através de manutenção adequada.",
        "O {make} {model} {year} híbrido bem mantido é sinônimo de investimento inteligente que combina economia, performance e responsabilidade ambiental.",

        // Grupo 3: Eficiência e performance
        "A eficiência excepcional do {make} {model} {year} híbrido depende do funcionamento perfeito de sistemas complexos e interdependentes. O cronograma de revisões é fundamental para manter essa eficiência.",
        "A performance inteligente do {make} {model} {year} híbrido se preserva através de cuidados especializados que mantêm a integração entre motor elétrico e combustão funcionando harmonicamente.",
        "Manter a economia de combustível superior do {make} {model} {year} híbrido exige atenção especializada aos sistemas que tornam essa eficiência possível.",
        "A tecnologia de propulsão dual do {make} {model} {year} híbrido oferece benefícios únicos que se mantêm apenas com manutenção adequada e especializada.",

        // Grupo 4: Sustentabilidade e futuro
        "Escolher um {make} {model} {year} híbrido demonstra consciência ambiental que se estende à manutenção responsável. Cada revisão contribui para preservar os benefícios ecológicos da hibridização.",
        "A sustentabilidade do {make} {model} {year} híbrido vai além de sua operação eficiente, incluindo manutenção que preserve essas características ambientalmente responsáveis.",
        "Manter o {make} {model} {year} híbrido adequadamente é investir no futuro da mobilidade sustentável e na preservação de tecnologias que beneficiam o meio ambiente.",

        // Grupo 5: Confiabilidade e segurança
        "A confiabilidade dos sistemas integrados do {make} {model} {year} híbrido depende fundamentalmente das verificações especializadas realizadas nas revisões programadas.",
        "Dirigir com tranquilidade no {make} {model} {year} híbrido significa confiar nos sistemas complexos adequadamente mantidos através do cronograma especializado.",
        "A segurança dos sistemas dual do {make} {model} {year} híbrido é garantida através de manutenção que considera as particularidades da tecnologia híbrida.",

        // Grupo 6: Experiência e satisfação
        "A experiência refinada de dirigir um {make} {model} {year} híbrido se preserva através de manutenção que mantém todos os sistemas funcionando em perfeita harmonia.",
        "Proprietários satisfeitos do {make} {model} {year} híbrido compartilham o cuidado com a manutenção especializada que preserva as características únicas desta tecnologia.",
        "A revolução na eficiência automotiva proporcionada pelo {make} {model} {year} híbrido se mantém através de revisões que honram a sofisticação desta tecnologia avançada."
    ];

    // CORREÇÃO: Estilos de manutenção completos para veículos híbridos
    private array $maintenanceStyles = [
        'tecnico_hibrido' => [
            'oil_change' => 'Substituição do óleo lubrificante e filtro com especificação para motores híbridos',
            'battery_hybrid_check' => 'Diagnóstico eletrônico da bateria híbrida e sistema de gerenciamento',
            'brake_regen' => 'Verificação e calibração do sistema de freios regenerativos',
            'hybrid_integration' => 'Análise da integração entre motor elétrico e combustão',
            'cooling_dual' => 'Verificação dos sistemas de arrefecimento duplo (motor e bateria)',
            'transmission_ecvt' => 'Inspeção da transmissão híbrida e-CVT',
            'brake_check' => 'Verificação do sistema de freios convencional',
            'electrical_safety' => 'Verificação de segurança dos sistemas elétricos',
            'hybrid_diagnostics' => 'Diagnóstico eletrônico dos sistemas híbridos',
            'air_filter' => 'Substituição do filtro de ar do motor',
            'spark_plugs_check' => 'Verificação das velas de ignição',
            'battery_hybrid_state' => 'Verificação do estado da bateria híbrida',
            'suspension_check' => 'Inspeção das suspensões',
            'ac_filter' => 'Troca do filtro de ar-condicionado',
            'battery_thermal_mgmt' => 'Verificação do gerenciamento térmico da bateria',
            'injection_clean' => 'Limpeza do sistema de injeção',
            'clutch_hybrid_check' => 'Inspeção do sistema de embreagem híbrida',
            'electrical_full' => 'Verificação completa dos sistemas elétricos',
            'brake_fluid_change' => 'Troca do fluido de freio',
            'hoses_check' => 'Verificação de mangueiras e tubulações',
            'fuel_filter' => 'Substituição do filtro de combustível',
            'belts_check' => 'Verificação das correias',
            'battery_advanced_diag' => 'Diagnóstico avançado da bateria híbrida',
            'cooling_systems_dual' => 'Verificação dos sistemas de arrefecimento duplo',
            'power_steering_check' => 'Verificação da direção assistida',
            'brake_regen_full' => 'Verificação completa dos freios regenerativos',
            'engine_mounts' => 'Verificação dos coxins do motor',
            'hybrid_electronics' => 'Verificação da eletrônica híbrida',
            'complete_revision' => 'Revisão ampla de todos os sistemas',
            'spark_plugs_change' => 'Substituição das velas de ignição',
            'belts_change' => 'Substituição de correias auxiliares',
            'battery_capacity_analysis' => 'Análise da capacidade da bateria híbrida',
            'hybrid_optimization' => 'Otimização dos sistemas híbridos'
        ],
        'simples_hibrido' => [
            'oil_change' => 'Troca de óleo e filtro',
            'battery_hybrid_check' => 'Verificação da bateria híbrida',
            'brake_regen' => 'Freios regenerativos',
            'hybrid_integration' => 'Sistema híbrido',
            'cooling_dual' => 'Refrigeração dupla',
            'transmission_ecvt' => 'Transmissão híbrida',
            'brake_check' => 'Verificar freios',
            'electrical_safety' => 'Segurança elétrica',
            'hybrid_diagnostics' => 'Diagnóstico híbrido',
            'air_filter' => 'Filtro de ar',
            'spark_plugs_check' => 'Verificar velas',
            'battery_hybrid_state' => 'Estado da bateria',
            'suspension_check' => 'Verificar suspensão',
            'ac_filter' => 'Filtro ar-condicionado',
            'battery_thermal_mgmt' => 'Gerenciamento térmico',
            'injection_clean' => 'Limpeza injeção',
            'clutch_hybrid_check' => 'Embreagem híbrida',
            'electrical_full' => 'Sistema elétrico',
            'brake_fluid_change' => 'Trocar fluido freio',
            'hoses_check' => 'Verificar mangueiras',
            'fuel_filter' => 'Filtro combustível',
            'belts_check' => 'Verificar correias',
            'battery_advanced_diag' => 'Diagnóstico bateria',
            'cooling_systems_dual' => 'Refrigeração dupla',
            'power_steering_check' => 'Direção assistida',
            'brake_regen_full' => 'Freios regenerativos',
            'engine_mounts' => 'Coxins motor',
            'hybrid_electronics' => 'Eletrônica híbrida',
            'complete_revision' => 'Revisão geral',
            'spark_plugs_change' => 'Trocar velas',
            'belts_change' => 'Trocar correias',
            'battery_capacity_analysis' => 'Análise bateria',
            'hybrid_optimization' => 'Otimização híbrida'
        ],
        'detalhado_hibrido' => [
            'oil_change' => 'Drenagem completa e reposição do óleo com viscosidade específica para motores híbridos',
            'battery_hybrid_check' => 'Análise detalhada da capacidade e eficiência da bateria de alta tensão híbrida',
            'brake_regen' => 'Inspeção minuciosa e recalibração do sistema de recuperação de energia cinética',
            'hybrid_integration' => 'Verificação completa da sincronização entre propulsão elétrica e combustão',
            'cooling_dual' => 'Diagnóstico avançado dos sistemas de gerenciamento térmico integrados',
            'transmission_ecvt' => 'Análise completa da transmissão continuamente variável eletrônica',
            'brake_check' => 'Verificação detalhada do sistema de freios convencional',
            'electrical_safety' => 'Inspeção completa da segurança dos sistemas elétricos',
            'hybrid_diagnostics' => 'Diagnóstico avançado de todos os sistemas híbridos',
            'air_filter' => 'Substituição e verificação do filtro de ar do motor',
            'spark_plugs_check' => 'Verificação detalhada das velas de ignição',
            'battery_hybrid_state' => 'Análise completa do estado da bateria híbrida',
            'suspension_check' => 'Inspeção detalhada das suspensões',
            'ac_filter' => 'Substituição do filtro de ar-condicionado com verificação',
            'battery_thermal_mgmt' => 'Verificação avançada do gerenciamento térmico da bateria',
            'injection_clean' => 'Limpeza completa do sistema de injeção',
            'clutch_hybrid_check' => 'Inspeção detalhada do sistema de embreagem híbrida',
            'electrical_full' => 'Verificação completa de todos os sistemas elétricos',
            'brake_fluid_change' => 'Troca completa do fluido de freio com sangria',
            'hoses_check' => 'Inspeção detalhada de mangueiras e tubulações',
            'fuel_filter' => 'Substituição do filtro de combustível com verificação',
            'belts_check' => 'Verificação detalhada das correias',
            'battery_advanced_diag' => 'Diagnóstico avançado da bateria híbrida com análise',
            'cooling_systems_dual' => 'Verificação completa dos sistemas de arrefecimento duplo',
            'power_steering_check' => 'Verificação detalhada da direção assistida',
            'brake_regen_full' => 'Verificação completa dos freios regenerativos',
            'engine_mounts' => 'Verificação dos coxins do motor',
            'hybrid_electronics' => 'Verificação completa da eletrônica híbrida',
            'complete_revision' => 'Revisão ampla de todos os sistemas do veículo',
            'spark_plugs_change' => 'Substituição das velas de ignição',
            'belts_change' => 'Substituição de correias auxiliares',
            'battery_capacity_analysis' => 'Análise detalhada da capacidade da bateria híbrida',
            'hybrid_optimization' => 'Otimização completa dos sistemas híbridos'
        ],
        'premium_hibrido' => [
            'oil_change' => 'Substituição premium do óleo com aditivos específicos para tecnologia híbrida',
            'battery_hybrid_check' => 'Diagnóstico premium da bateria com análise preditiva de degradação',
            'brake_regen' => 'Otimização avançada do sistema de recuperação energética',
            'hybrid_integration' => 'Calibração de precisão da integração dual de propulsão',
            'cooling_dual' => 'Otimização térmica dos sistemas de arrefecimento integrados',
            'transmission_ecvt' => 'Verificação especializada da transmissão híbrida de alta tecnologia',
            'brake_check' => 'Análise premium dos freios convencionais',
            'electrical_safety' => 'Verificação premium da segurança elétrica',
            'hybrid_diagnostics' => 'Diagnóstico premium dos sistemas híbridos',
            'air_filter' => 'Filtro de ar premium',
            'spark_plugs_check' => 'Velas de ignição premium',
            'battery_hybrid_state' => 'Estado premium da bateria',
            'suspension_check' => 'Suspensão premium',
            'ac_filter' => 'Filtro ar-condicionado premium',
            'battery_thermal_mgmt' => 'Gerenciamento térmico premium',
            'injection_clean' => 'Limpeza premium da injeção',
            'clutch_hybrid_check' => 'Embreagem híbrida premium',
            'electrical_full' => 'Sistema elétrico premium',
            'brake_fluid_change' => 'Fluido de freio premium',
            'hoses_check' => 'Mangueiras premium',
            'fuel_filter' => 'Filtro combustível premium',
            'belts_check' => 'Correias premium',
            'battery_advanced_diag' => 'Diagnóstico premium da bateria',
            'cooling_systems_dual' => 'Refrigeração dupla premium',
            'power_steering_check' => 'Direção assistida premium',
            'brake_regen_full' => 'Freios regenerativos premium',
            'engine_mounts' => 'Coxins motor premium',
            'hybrid_electronics' => 'Eletrônica híbrida premium',
            'complete_revision' => 'Revisão premium completa',
            'spark_plugs_change' => 'Velas premium',
            'belts_change' => 'Correias premium',
            'battery_capacity_analysis' => 'Análise premium da bateria',
            'hybrid_optimization' => 'Otimização premium'
        ]
    ];

    // CORREÇÃO: Cronograma base completo para veículos híbridos com 6 revisões
    private array $maintenanceSchedule = [
        '10.000 km ou 12 meses' => [
            'oil_change',
            'battery_hybrid_check',
            'brake_check',
            'cooling_dual',
            'electrical_safety',
            'hybrid_diagnostics'
        ],
        '20.000 km ou 24 meses' => [
            'oil_change',
            'air_filter',
            'spark_plugs_check',
            'brake_regen',
            'battery_hybrid_state',
            'suspension_check',
            'ac_filter'
        ],
        '30.000 km ou 36 meses' => [
            'oil_change',
            'battery_thermal_mgmt',
            'injection_clean',
            'clutch_hybrid_check',
            'electrical_full',
            'brake_fluid_change',
            'hoses_check'
        ],
        '40.000 km ou 48 meses' => [
            'oil_change',
            'air_filter',
            'fuel_filter',
            'brake_fluid_change',
            'belts_check',
            'hybrid_integration',
            'battery_advanced_diag'
        ],
        '50.000 km ou 60 meses' => [
            'oil_change',
            'cooling_systems_dual',
            'power_steering_check',
            'brake_regen_full',
            'engine_mounts',
            'hybrid_electronics'
        ],
        '60.000 km ou 72 meses' => [
            'complete_revision',
            'oil_change',
            'spark_plugs_change',
            'belts_change',
            'transmission_ecvt',
            'battery_capacity_analysis',
            'hybrid_optimization'
        ]
    ];

    // Variações de FAQs para híbridos
    private array $faqVariations = [
        'basico_hibrido' => [
            'battery_maintenance' => "Preciso fazer manutenção na bateria do meu {make} {model} híbrido?",
            'oil_frequency' => 'Os veículos híbridos precisam de trocas de óleo com a mesma frequência?',
            'regenerative_brakes' => "O sistema regenerativo de freios do {make} {model} precisa de manutenção especial?",
            'maintenance_cost' => 'As revisões de híbridos são mais caras que carros convencionais?'
        ],
        'completo_hibrido' => [
            'battery_maintenance' => "Como é feita a manutenção da bateria híbrida do {make} {model}?",
            'oil_frequency' => 'Qual a diferença na frequência de troca de óleo em híbridos?',
            'regenerative_brakes' => "Como funciona a manutenção dos freios regenerativos?",
            'maintenance_cost' => 'Qual o custo adicional de manutenção de um veículo híbrido?',
            'specialized_service' => 'Qualquer oficina pode fazer manutenção em híbridos?',
            'fuel_economy' => 'A manutenção afeta a economia de combustível do híbrido?'
        ],
        'pratico_hibrido' => [
            'maintenance_frequency' => "Com que frequência devo revisar meu {make} {model} híbrido?",
            'battery_lifespan' => 'Quanto tempo dura a bateria de um híbrido?',
            'driving_modes' => 'Os modos de condução afetam a manutenção?',
            'warranty_hybrid' => 'Como funciona a garantia dos sistemas híbridos?'
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
        return [
            ['revisao' => '1ª Revisão', 'intervalo' => '10.000 km ou 12 meses', 'principais_servicos' => 'Troca de óleo, verificação sistema híbrido', 'estimativa_custo' => $this->getCostRange($vehicleData, 1)],
            ['revisao' => '2ª Revisão', 'intervalo' => '20.000 km ou 24 meses', 'principais_servicos' => 'Óleo, filtros, freios regenerativos, bateria', 'estimativa_custo' => $this->getCostRange($vehicleData, 2)],
            ['revisao' => '3ª Revisão', 'intervalo' => '30.000 km ou 36 meses', 'principais_servicos' => 'Óleo, sistema térmico, transmissão híbrida', 'estimativa_custo' => $this->getCostRange($vehicleData, 3)],
            ['revisao' => '4ª Revisão', 'intervalo' => '40.000 km ou 48 meses', 'principais_servicos' => 'Óleo, filtros, diagnóstico avançado híbrido', 'estimativa_custo' => $this->getCostRange($vehicleData, 4)],
            ['revisao' => '5ª Revisão', 'intervalo' => '50.000 km ou 60 meses', 'principais_servicos' => 'Óleo, arrefecimento duplo, eletrônica híbrida', 'estimativa_custo' => $this->getCostRange($vehicleData, 5)],
            ['revisao' => '6ª Revisão', 'intervalo' => '60.000 km ou 72 meses', 'principais_servicos' => 'Revisão ampla, transmissão, otimização híbrida', 'estimativa_custo' => $this->getCostRange($vehicleData, 6)]
        ];
    }

    // CORREÇÃO: generateDetailedSchedule() para garantir exatamente 6 revisões
    public function generateDetailedSchedule(array $vehicleData): array
    {
        $style = $this->selectMaintenanceStyle($vehicleData);
        $schedule = [];

        // GARANTIR EXATAMENTE 6 REVISÕES - INTERVALOS FIXOS PARA HÍBRIDOS
        $revisionData = [
            1 => ['interval' => '10.000 km ou 12 meses', 'km' => '10.000'],
            2 => ['interval' => '20.000 km ou 24 meses', 'km' => '20.000'],
            3 => ['interval' => '30.000 km ou 36 meses', 'km' => '30.000'],
            4 => ['interval' => '40.000 km ou 48 meses', 'km' => '40.000'],
            5 => ['interval' => '50.000 km ou 60 meses', 'km' => '50.000'],
            6 => ['interval' => '60.000 km ou 72 meses', 'km' => '60.000']
        ];

        for ($revisionNumber = 1; $revisionNumber <= 6; $revisionNumber++) {
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

    // CORREÇÃO: Método auxiliar para garantir 6 revisões
    private function getServicesForRevision(int $revision, array $vehicleData): array
    {
        // Mapeamento direto das 6 revisões para garantir serviços específicos
        $servicesMap = [
            1 => ['oil_change', 'battery_hybrid_check', 'brake_check', 'cooling_dual', 'electrical_safety', 'hybrid_diagnostics'],
            2 => ['oil_change', 'air_filter', 'spark_plugs_check', 'brake_regen', 'battery_hybrid_state', 'suspension_check'],
            3 => ['oil_change', 'battery_thermal_mgmt', 'injection_clean', 'clutch_hybrid_check', 'electrical_full', 'brake_fluid_change'],
            4 => ['oil_change', 'fuel_filter', 'brake_fluid_change', 'belts_check', 'hybrid_integration', 'battery_advanced_diag'],
            5 => ['oil_change', 'cooling_systems_dual', 'power_steering_check', 'brake_regen_full', 'engine_mounts', 'hybrid_electronics'],
            6 => ['complete_revision', 'spark_plugs_change', 'belts_change', 'transmission_ecvt', 'battery_capacity_analysis', 'hybrid_optimization']
        ];

        // Retornar serviços específicos ou fallback
        return $servicesMap[$revision] ?? $this->getDefaultServicesForRevision($revision, $vehicleData);
    }

    private function getDefaultServicesForRevision(int $revision, array $vehicleData): array
    {
        // Serviços específicos para veículos híbridos como fallback
        switch ($revision) {
            case 1:
                return ['oil_change', 'hybrid_check', 'brake_check', 'battery_diagnostic'];
            case 2:
                return ['oil_change', 'air_filter', 'regenerative_brakes', 'hybrid_battery'];
            case 3:
                return ['oil_change', 'thermal_system', 'hybrid_transmission', 'cooling_dual'];
            case 4:
                return ['oil_change', 'fuel_filter', 'hybrid_diagnostic', 'integration_check'];
            case 5:
                return ['oil_change', 'dual_cooling', 'hybrid_electronics', 'efficiency_test'];
            case 6:
                return ['major_service', 'transmission_hybrid', 'optimization', 'complete_analysis'];
            default:
                return ['oil_change', 'hybrid_check'];
        }
    }

    public function generatePreventiveMaintenance(array $vehicleData): array
    {
        return [
            'verificacoes_mensais' => [
                'Verificar nível do óleo do motor',
                'Conferir nível de carga da bateria híbrida',
                'Calibrar pneus',
                'Testar funcionamento dos modos de condução'
            ],
            'verificacoes_trimestrais' => [
                'Fluido de freio',
                'Funcionamento do sistema regenerativo',
                'Desgaste dos pneus',
                'Sistemas de segurança híbridos'
            ],
            'verificacoes_anuais' => [
                'Eficiência do sistema híbrido integrado',
                'Integração motor elétrico/combustão',
                'Sistema de arrefecimento duplo',
                'Atualizações de software disponíveis',
                'Teste de autonomia em modo elétrico (se aplicável)'
            ]
        ];
    }

    public function generateCriticalParts(array $vehicleData): array
    {
        return [
            [
                'componente' => 'Bateria de Alta Tensão Híbrida',
                'intervalo_recomendado' => 'Diagnóstico a cada 20.000 km',
                'observacao' => 'Componente crítico que armazena energia para assistência elétrica'
            ],
            [
                'componente' => 'Sistema de Freios Regenerativos',
                'intervalo_recomendado' => 'Calibração a cada 30.000 km',
                'observacao' => 'Responsável pela recuperação de energia e economia de combustível'
            ],
            [
                'componente' => 'Transmissão Híbrida (e-CVT)',
                'intervalo_recomendado' => 'Verificação a cada 60.000 km',
                'observacao' => 'Sistema complexo que integra motor elétrico e combustão'
            ],
            [
                'componente' => 'Sistema de Arrefecimento Duplo',
                'intervalo_recomendado' => 'Inspeção a cada 40.000 km',
                'observacao' => 'Resfria tanto o motor a combustão quanto os componentes elétricos'
            ],
            [
                'componente' => 'Conversor DC-DC',
                'intervalo_recomendado' => 'Diagnóstico a cada 40.000 km',
                'observacao' => 'Componente que gerencia a energia entre os sistemas'
            ],
            [
                'componente' => 'Unidade de Controle Híbrida',
                'intervalo_recomendado' => 'Verificação a cada 30.000 km',
                'observacao' => 'Cérebro do sistema que coordena a operação dual'
            ]
        ];
    }

    public function generateTechnicalSpecs(array $vehicleData): array
    {
        return [
            'capacidade_oleo' => $this->getOilCapacity($vehicleData),
            'tipo_oleo_recomendado' => $this->getRecommendedOil($vehicleData),
            'intervalo_troca_oleo' => '10.000 km ou 12 meses',
            'capacidade_bateria_hibrida' => $this->getHybridBatteryCapacity($vehicleData),
            'fluido_freio' => 'DOT 4',
            'fluido_transmissao_hibrida' => 'Específico para e-CVT',
            'fluido_arrefecimento' => 'Específico para sistemas híbridos',
            'garantia_bateria' => $this->getBatteryWarranty($vehicleData),
            'pressao_pneus' => $this->getTirePressure($vehicleData)
        ];
    }

    public function generateWarrantyInfo(array $vehicleData): array
    {
        return [
            'prazo_garantia' => '3 anos ou 100.000 km',
            'garantia_bateria_hibrida' => $this->getBatteryWarranty($vehicleData),
            'garantia_sistemas_hibridos' => '5 anos ou 100.000 km',
            'observacoes_importantes' => 'A garantia da bateria híbrida pode ser invalidada se o cronograma de revisões não for seguido. Evite intervenções não autorizadas nos sistemas de alta tensão.',
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
        $year = $vehicleData['year'] ?? date('Y');

        // Agrupar veículos híbridos por faixas de ano e marca
        $yearGroup = floor($year / 3) * 3;
        $segment = $this->getHybridSegment($vehicleData);

        return "hybrid_{$make}_{$segment}_{$yearGroup}";
    }

    private function getHybridSegment(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        $model = strtolower($vehicleData['model'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');

        // Classificar por segmento híbrido
        if (in_array($make, ['lexus', 'bmw', 'mercedes-benz', 'audi', 'volvo'])) {
            return 'premium';
        } elseif (in_array($make, ['toyota', 'honda']) && strpos($model, 'prius') !== false) {
            return 'pioneiro';
        } elseif (in_array($category, ['suv']) || strpos($model, 'suv') !== false) {
            return 'suv_hibrido';
        } elseif (in_array($make, ['toyota', 'honda', 'hyundai', 'kia'])) {
            return 'popular';
        }

        return 'geral';
    }

    private function selectMaintenanceStyle(array $vehicleData): string
    {
        $segment = $this->getHybridSegment($vehicleData);
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
            $availableStyles = ['tecnico_hibrido']; // Fallback seguro
        }

        // Preferência por segmento
        $preferredStyles = [
            'premium' => ['premium_hibrido', 'detalhado_hibrido'],
            'pioneiro' => ['tecnico_hibrido', 'detalhado_hibrido'],
            'suv_hibrido' => ['detalhado_hibrido', 'tecnico_hibrido'],
            'popular' => ['simples_hibrido', 'tecnico_hibrido']
        ];

        $preferred = $preferredStyles[$segment] ?? ['tecnico_hibrido'];
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
        $styleData = $this->maintenanceStyles[$style] ?? $this->maintenanceStyles['tecnico_hibrido'];
        $services = [];

        foreach ($serviceKeys as $key) {
            if (isset($styleData[$key])) {
                $services[] = $styleData[$key];
            } else {
                $services[] = $this->getHybridServiceFallback($key);
            }
        }

        return $services;
    }

    private function getHybridServiceFallback(string $serviceKey): string
    {
        $fallbacks = [
            'brake_check' => 'Verificação do sistema de freios convencional',
            'electrical_safety' => 'Verificação de segurança dos sistemas elétricos',
            'hybrid_diagnostics' => 'Diagnóstico eletrônico dos sistemas híbridos',
            'air_filter' => 'Substituição do filtro de ar do motor',
            'spark_plugs_check' => 'Verificação das velas de ignição',
            'battery_hybrid_state' => 'Verificação do estado da bateria híbrida',
            'suspension_check' => 'Inspeção das suspensões',
            'ac_filter' => 'Troca do filtro de ar-condicionado',
            'battery_thermal_mgmt' => 'Verificação do gerenciamento térmico da bateria',
            'injection_clean' => 'Limpeza do sistema de injeção',
            'clutch_hybrid_check' => 'Inspeção do sistema de embreagem híbrida',
            'electrical_full' => 'Verificação completa dos sistemas elétricos',
            'brake_fluid_change' => 'Troca do fluido de freio',
            'hoses_check' => 'Verificação de mangueiras e tubulações',
            'fuel_filter' => 'Substituição do filtro de combustível',
            'belts_check' => 'Verificação das correias',
            'battery_advanced_diag' => 'Diagnóstico avançado da bateria híbrida',
            'cooling_systems_dual' => 'Verificação dos sistemas de arrefecimento duplo',
            'power_steering_check' => 'Verificação da direção assistida',
            'brake_regen_full' => 'Verificação completa dos freios regenerativos',
            'engine_mounts' => 'Verificação dos coxins do motor',
            'hybrid_electronics' => 'Verificação da eletrônica híbrida',
            'complete_revision' => 'Revisão ampla de todos os sistemas',
            'spark_plugs_change' => 'Substituição das velas de ignição',
            'belts_change' => 'Substituição de correias auxiliares',
            'battery_capacity_analysis' => 'Análise da capacidade da bateria híbrida',
            'hybrid_optimization' => 'Otimização dos sistemas híbridos',
            // Fallbacks adicionais para serviços do getDefaultServicesForRevision
            'hybrid_check' => 'Verificação do sistema híbrido',
            'battery_diagnostic' => 'Diagnóstico da bateria híbrida',
            'regenerative_brakes' => 'Verificação dos freios regenerativos',
            'hybrid_battery' => 'Verificação da bateria híbrida',
            'thermal_system' => 'Verificação do sistema térmico',
            'hybrid_transmission' => 'Verificação da transmissão híbrida',
            'hybrid_diagnostic' => 'Diagnóstico dos sistemas híbridos',
            'integration_check' => 'Verificação da integração dos sistemas',
            'dual_cooling' => 'Verificação do arrefecimento duplo',
            'efficiency_test' => 'Teste de eficiência',
            'major_service' => 'Revisão principal',
            'transmission_hybrid' => 'Verificação da transmissão híbrida',
            'optimization' => 'Otimização do sistema',
            'complete_analysis' => 'Análise completa dos sistemas'
        ];

        return $fallbacks[$serviceKey] ?? 'Serviço especializado para veículos híbridos';
    }

    private function getCostRange(array $vehicleData, int $revisionNumber): string
    {
        $segment = $this->getHybridSegment($vehicleData);
        $year = $vehicleData['year'] ?? date('Y');

        // Custos base por revisão para híbridos (ligeiramente maiores que convencionais)
        $baseCosts = [
            1 => [420, 500],
            2 => [580, 650],
            3 => [720, 800],
            4 => [900, 1000],
            5 => [650, 750],
            6 => [1200, 1400]
        ];

        $base = $baseCosts[$revisionNumber] ?? [600, 700];

        // Ajustar por segmento
        $multipliers = [
            'premium' => 1.5,
            'pioneiro' => 1.1,
            'suv_hibrido' => 1.3,
            'popular' => 0.9,
            'geral' => 1.0
        ];

        $multiplier = $multipliers[$segment] ?? 1.0;

        // Veículos híbridos mais novos podem ter custos maiores devido à especialização
        $currentYear = date('Y');
        if (($currentYear - $year) <= 3) {
            $multiplier *= 1.1;
        }

        $min = round($base[0] * $multiplier);
        $max = round($base[1] * $multiplier);

        return "R$ {$min} - R$ {$max}";
    }

    private function getVariedObservation(int $revisionNumber, array $vehicleData): string
    {
        $observations = [
            1 => [
                'Primeira revisão importante para verificar a integração inicial dos sistemas híbridos.',
                'Esta revisão inicial confirma o funcionamento adequado da propulsão dual.',
                'Fundamental para estabelecer o histórico de manutenção dos sistemas híbridos.',
                'Revisão de adaptação que verifica se a integração motor elétrico/combustão está funcionando perfeitamente.',
                'Primeira verificação completa dos sistemas de segurança híbridos após o período inicial.'
            ],
            4 => [
                'Revisão crítica que inclui diagnóstico avançado da bateria híbrida e sistemas de integração.',
                'Momento ideal para avaliação da eficiência dos sistemas de propulsão dual.',
                'Esta revisão foca na verificação de componentes específicos da tecnologia híbrida.',
                'Verificação crucial para manter a economia de combustível característica dos híbridos.'
            ],
            6 => [
                'Revisão ampla que inclui análise detalhada de todos os componentes híbridos e atualizações.',
                'Momento importante para otimização dos sistemas e verificação de componentes críticos.',
                'Verificação completa que avalia a harmonia entre os sistemas após uso prolongado.',
                'Revisão de renovação que otimiza a integração dos sistemas híbridos.',
                'Verificação abrangente que inclui atualizações de software e melhorias disponíveis.'
            ]
        ];

        $defaultObs = [
            'Revisão importante para manter a eficiência dos sistemas híbridos integrados.',
            'Verificação essencial para preservar a economia de combustível e performance.',
            'Manutenção preventiva especializada para tecnologia de propulsão dual.',
            'Inspeção programada para detectar e prevenir problemas nos sistemas complexos.'
        ];

        $availableObs = $observations[$revisionNumber] ?? $defaultObs;
        return $availableObs[array_rand($availableObs)];
    }

    private function getFAQStyle(string $vehicleKey): string
    {
        $usedStyles = self::$usedFAQStyles[$vehicleKey] ?? [];
        $availableStyles = array_diff(['basico_hibrido', 'completo_hibrido', 'pratico_hibrido'], $usedStyles);

        if (empty($availableStyles)) {
            self::$usedFAQStyles[$vehicleKey] = [];
            $availableStyles = ['basico_hibrido', 'completo_hibrido', 'pratico_hibrido'];
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
            'battery_maintenance' => [
                'basico_hibrido' => 'A bateria híbrida não requer manutenção direta, mas deve ser verificada nas revisões.',
                'completo_hibrido' => 'A bateria híbrida é selada e não requer manutenção do proprietário, mas precisa de diagnósticos especializados durante as revisões.',
                'pratico_hibrido' => 'Não há manutenção direta da bateria, apenas verificações nas revisões programadas.'
            ],
            'oil_frequency' => [
                'basico_hibrido' => 'Sim, híbridos ainda precisam de trocas de óleo regulares no motor a combustão.',
                'completo_hibrido' => 'O motor a combustão dos híbridos requer trocas de óleo, mas alguns fabricantes estendem os intervalos devido ao menor uso.',
                'pratico_hibrido' => 'Precisa trocar óleo normalmente, mas pode durar um pouco mais que carros convencionais.'
            ],
            'regenerative_brakes' => [
                'basico_hibrido' => 'Os freios regenerativos precisam de calibração durante as revisões.',
                'completo_hibrido' => 'O sistema regenerativo requer verificação da calibração e eficiência durante as revisões para garantir economia de energia.',
                'pratico_hibrido' => 'Freios regenerativos precisam de verificação especial nas revisões.'
            ],
            'maintenance_cost' => [
                'basico_hibrido' => 'Geralmente sim, devido à complexidade dos sistemas.',
                'completo_hibrido' => 'Os custos são ligeiramente maiores devido à necessidade de diagnósticos especializados e técnicos qualificados.',
                'pratico_hibrido' => 'Um pouco mais caro, mas a economia de combustível compensa.'
            ],
            'maintenance_frequency' => 'Geralmente a cada 10.000 km ou 12 meses, similar aos convencionais.',
            'battery_lifespan' => 'Baterias híbridas modernas duram entre 8-15 anos, com garantia de 8 anos.',
            'driving_modes' => 'Usar modo ECO regularmente ajuda a preservar a bateria e reduzir desgaste.',
            'warranty_hybrid' => 'Sistemas híbridos têm garantia estendida, geralmente 8 anos para a bateria.',
            'specialized_service' => 'Recomenda-se oficinas com técnicos treinados em tecnologia híbrida.',
            'fuel_economy' => 'Sim, manutenção adequada preserva a eficiência dos sistemas e a economia de combustível.'
        ];

        if (isset($answers[$questionKey])) {
            if (is_array($answers[$questionKey])) {
                return $answers[$questionKey][$style] ?? $answers[$questionKey][array_key_first($answers[$questionKey])];
            }
            return $answers[$questionKey];
        }

        return 'Consulte o manual do proprietário ou uma oficina especializada em veículos híbridos.';
    }

    private function getLifeTips(array $vehicleData): array
    {
        $segment = $this->getHybridSegment($vehicleData);

        $baseTips = [
            'Use o modo ECO regularmente para otimizar a bateria',
            'Evite descarregar completamente a bateria híbrida',
            'Mantenha o software do sistema sempre atualizado',
            'Use combustível de qualidade para o motor a combustão',
            'Permita que o sistema gerencie automaticamente a energia',
            'Realize manutenções apenas em oficinas especializadas'
        ];

        // Adicionar dicas específicas por segmento
        if ($segment === 'premium') {
            $baseTips[] = 'Utilize apenas peças originais para sistemas híbridos';
            $baseTips[] = 'Agende atualizações de software na concessionária';
        } elseif ($segment === 'pioneiro') {
            $baseTips[] = 'Aproveite a experiência acumulada da marca em híbridos';
            $baseTips[] = 'Monitore regularmente a economia de combustível';
        }

        return $baseTips;
    }

    private function getOilCapacity(array $vehicleData): string
    {
        // Híbridos geralmente usam motores menores
        $model = strtolower($vehicleData['model'] ?? '');
        $make = strtolower($vehicleData['make'] ?? '');

        if (strpos($model, 'prius') !== false) return '3.9 litros';
        if (strpos($model, 'corolla') !== false && $make === 'toyota') return '4.4 litros';
        if (strpos($model, 'camry') !== false && $make === 'toyota') return '4.8 litros';
        if (strpos($model, 'accord') !== false && $make === 'honda') return '4.4 litros';
        if (strpos($model, 'insight') !== false) return '3.7 litros';

        return '4.0 litros';
    }

    private function getRecommendedOil(array $vehicleData): string
    {
        // Híbridos geralmente usam óleos de baixa viscosidade para eficiência
        $year = $vehicleData['year'] ?? date('Y');
        $make = strtolower($vehicleData['make'] ?? '');

        if ($year >= 2020) {
            return '0W20 Sintético, específico para motores híbridos';
        } elseif ($make === 'toyota' || $make === 'honda') {
            return '0W20 ou 5W30 Sintético para híbridos';
        }

        return '5W30 Sintético, adequado para sistemas híbridos';
    }

    private function getHybridBatteryCapacity(array $vehicleData): string
    {
        $model = strtolower($vehicleData['model'] ?? '');
        $make = strtolower($vehicleData['make'] ?? '');

        if (strpos($model, 'prius') !== false) return '1.3 kWh (Ni-MH)';
        if (strpos($model, 'corolla') !== false && $make === 'toyota') return '1.3 kWh (Li-ion)';
        if (strpos($model, 'camry') !== false && $make === 'toyota') return '1.6 kWh (Li-ion)';
        if (strpos($model, 'accord') !== false && $make === 'honda') return '1.3 kWh (Li-ion)';
        if (strpos($model, 'insight') !== false) return '1.1 kWh (Li-ion)';
        if ($make === 'hyundai' || $make === 'kia') return '1.56 kWh (Li-ion)';

        return '1.56 kWh (Li-ion)';
    }

    private function getBatteryWarranty(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');

        return match ($make) {
            'toyota' => '8 anos ou 160.000 km',
            'honda' => '8 anos ou 160.000 km',
            'hyundai' => '8 anos ou 160.000 km',
            'kia' => '7 anos ou 150.000 km',
            'lexus' => '8 anos ou 160.000 km',
            'bmw', 'mercedes-benz', 'audi' => '8 anos ou 160.000 km',
            default => '8 anos ou 160.000 km'
        };
    }

    private function getTirePressure(array $vehicleData): string
    {
        $emptyFront = $vehicleData['pressure_empty_front'] ?? 32;
        $emptyRear = $vehicleData['pressure_empty_rear'] ?? 32;

        return "Dianteiros: {$emptyFront} PSI | Traseiros: {$emptyRear} PSI (veículo vazio)";
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

    // Métodos mantidos para compatibilidade
    private function estimateCost(string $km): string
    {
        $kmInt = (int)str_replace('.', '', $km);

        return match (true) {
            $kmInt <= 10000 => 'R$ 420 - R$ 500',
            $kmInt <= 20000 => 'R$ 580 - R$ 650',
            $kmInt <= 30000 => 'R$ 720 - R$ 800',
            $kmInt <= 40000 => 'R$ 900 - R$ 1.000',
            $kmInt <= 50000 => 'R$ 650 - R$ 750',
            default => 'R$ 1.200 - R$ 1.400'
        };
    }

    private function getIntervalObservations(string $km, int $revisionNumber): string
    {
        return $this->getVariedObservation($revisionNumber, []);
    }
}
