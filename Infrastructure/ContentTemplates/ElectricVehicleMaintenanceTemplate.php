<?php

namespace Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates;

class ElectricVehicleMaintenanceTemplate
{
    // Sistema de controle de variações usadas
    private static array $usedIntros = [];
    private static array $usedConclusions = [];
    private static array $usedMaintenanceStyles = [];
    private static array $usedFAQStyles = [];

    // CORREÇÃO: Cronograma específico para veículos elétricos
    private array $maintenanceSchedule = [
        '10.000 km' => ['brake_check', 'ac_filter', 'electrical_basic', 'tire_check'],
        '20.000 km' => ['brake_fluid_check', 'suspension_check', 'electrical_full', 'tire_rotation'],
        '30.000 km' => ['cooling_check', 'battery_thermal', 'high_voltage_connectors', 'auxiliary_systems'],
        '40.000 km' => ['brake_fluid_change', 'hoses_check', 'alignment_check', 'propulsion_system'],
        '50.000 km' => ['cooling_battery_fluid', 'suspension_full', 'brake_regen_full', 'battery_advanced'],
        '60.000 km' => ['connectivity_check', 'battery_full_analysis', 'cooling_system_complete', 'complete_revision']
    ];

    // CORREÇÃO: Estilos de manutenção específicos para veículos elétricos
    private array $maintenanceStyles = [
        'tecnico_eletrico' => [
            'brake_check' => 'Verificação do sistema de freios regenerativos',
            'ac_filter' => 'Substituição do filtro de ar-condicionado',
            'electrical_basic' => 'Verificação básica dos sistemas elétricos',
            'tire_check' => 'Inspeção da calibragem e desgaste dos pneus',
            'brake_fluid_check' => 'Verificação do nível do fluido de freio',
            'suspension_check' => 'Inspeção da suspensão',
            'electrical_full' => 'Diagnóstico completo dos sistemas elétricos',
            'tire_rotation' => 'Rodízio dos pneus',
            'cooling_check' => 'Verificação do sistema de refrigeração',
            'battery_thermal' => 'Sistema de gerenciamento térmico da bateria',
            'high_voltage_connectors' => 'Inspeção dos conectores de alta tensão',
            'auxiliary_systems' => 'Verificação dos sistemas auxiliares',
            'brake_fluid_change' => 'Troca do fluido de freio',
            'hoses_check' => 'Verificação de mangueiras e tubulações',
            'alignment_check' => 'Verificação do alinhamento',
            'propulsion_system' => 'Verificação do sistema de propulsão elétrica',
            'cooling_battery_fluid' => 'Verificação do fluido de refrigeração da bateria',
            'suspension_full' => 'Inspeção completa da suspensão',
            'brake_regen_full' => 'Verificação completa dos freios regenerativos',
            'battery_advanced' => 'Diagnóstico avançado da bateria',
            'connectivity_check' => 'Verificação dos sistemas de conectividade',
            'battery_full_analysis' => 'Análise completa da bateria de alta tensão',
            'cooling_system_complete' => 'Verificação completa do sistema de refrigeração',
            'complete_revision' => 'Revisão ampla de todos os sistemas'
        ],
        // ADICIONAR os estilos que estão sendo referenciados mas não existem:
        'simples_eletrico' => [
            'brake_check' => 'Verificar freios',
            'ac_filter' => 'Trocar filtro do ar',
            'electrical_basic' => 'Verificar sistemas elétricos',
            'tire_check' => 'Verificar pneus',
            'brake_fluid_check' => 'Verificar fluido de freio',
            'suspension_check' => 'Verificar suspensão',
            'electrical_full' => 'Revisar sistema elétrico',
            'tire_rotation' => 'Fazer rodízio de pneus',
            'cooling_check' => 'Verificar refrigeração',
            'battery_thermal' => 'Verificar bateria',
            'high_voltage_connectors' => 'Verificar conectores',
            'auxiliary_systems' => 'Verificar auxiliares',
            'brake_fluid_change' => 'Trocar fluido freio',
            'hoses_check' => 'Verificar mangueiras',
            'alignment_check' => 'Verificar alinhamento',
            'propulsion_system' => 'Verificar propulsão',
            'cooling_battery_fluid' => 'Verificar refrigeração bateria',
            'suspension_full' => 'Suspensão completa',
            'brake_regen_full' => 'Freios regenerativos',
            'battery_advanced' => 'Bateria avançada',
            'connectivity_check' => 'Verificar conectividade',
            'battery_full_analysis' => 'Análise da bateria',
            'cooling_system_complete' => 'Refrigeração completa',
            'complete_revision' => 'Revisão geral'
        ],
        'detalhado_eletrico' => [
            'brake_check' => 'Verificação minuciosa do sistema de freios regenerativos e convencionais',
            'ac_filter' => 'Substituição do filtro de ar-condicionado com verificação do sistema',
            'electrical_basic' => 'Diagnóstico básico dos sistemas elétricos de baixa tensão',
            'tire_check' => 'Inspeção detalhada da calibragem e desgaste dos pneumáticos',
            'brake_fluid_check' => 'Verificação criteriosa do nível e qualidade do fluido de freio',
            'suspension_check' => 'Inspeção completa da suspensão dianteira e traseira',
            'electrical_full' => 'Diagnóstico avançado de todos os sistemas elétricos',
            'tire_rotation' => 'Rodízio dos pneus com verificação de alinhamento',
            'cooling_check' => 'Análise do sistema de refrigeração da bateria e motor',
            'battery_thermal' => 'Verificação do sistema de gerenciamento térmico da bateria',
            'high_voltage_connectors' => 'Inspeção detalhada dos conectores de alta tensão',
            'auxiliary_systems' => 'Verificação completa dos sistemas auxiliares',
            'brake_fluid_change' => 'Troca do fluido de freio com sangria do sistema',
            'hoses_check' => 'Inspeção de mangueiras e tubulações do sistema',
            'alignment_check' => 'Verificação e correção do alinhamento das rodas',
            'propulsion_system' => 'Diagnóstico completo do sistema de propulsão elétrica',
            'cooling_battery_fluid' => 'Verificação do fluido de refrigeração da bateria',
            'suspension_full' => 'Inspeção completa da suspensão e amortecedores',
            'brake_regen_full' => 'Verificação completa dos freios regenerativos',
            'battery_advanced' => 'Diagnóstico avançado da bateria de alta tensão',
            'connectivity_check' => 'Verificação dos sistemas de conectividade e infotainment',
            'battery_full_analysis' => 'Análise completa da capacidade e saúde da bateria',
            'cooling_system_complete' => 'Verificação completa do sistema de refrigeração',
            'complete_revision' => 'Revisão ampla de todos os sistemas do veículo'
        ],
        'premium_eletrico' => [
            'brake_check' => 'Análise técnica dos freios regenerativos',
            'ac_filter' => 'Filtro premium do ar-condicionado',
            'electrical_basic' => 'Sistemas elétricos de baixa tensão',
            'tire_check' => 'Pneumáticos especiais',
            'brake_fluid_check' => 'Fluido de freio especializado',
            'suspension_check' => 'Suspensão adaptativa',
            'electrical_full' => 'Sistemas elétricos avançados',
            'tire_rotation' => 'Rodízio premium',
            'cooling_check' => 'Refrigeração inteligente',
            'battery_thermal' => 'Gerenciamento térmico avançado',
            'high_voltage_connectors' => 'Conectores de alta performance',
            'auxiliary_systems' => 'Sistemas auxiliares premium',
            'brake_fluid_change' => 'Fluido de freio premium',
            'hoses_check' => 'Tubulações especializadas',
            'alignment_check' => 'Alinhamento de precisão',
            'propulsion_system' => 'Propulsão de alta performance',
            'cooling_battery_fluid' => 'Refrigeração especializada',
            'suspension_full' => 'Suspensão premium',
            'brake_regen_full' => 'Freios regenerativos avançados',
            'battery_advanced' => 'Bateria de alta tecnologia',
            'connectivity_check' => 'Conectividade premium',
            'battery_full_analysis' => 'Análise técnica da bateria',
            'cooling_system_complete' => 'Sistema de refrigeração premium',
            'complete_revision' => 'Revisão premium completa'
        ]
    ];

    // 50 variações de introdução específicas para veículos elétricos
    private array $intros = [
        // Grupo 1: Técnicas e inovação (5 existentes + 5 novas)
        "A manutenção do seu {make} {model} {year} elétrico tem características especiais. Embora geralmente exija menos intervenções que veículos a combustão, seu cronograma de revisões contém verificações específicas para o sistema elétrico e bateria.",
        "O {make} {model} {year} elétrico, apesar de ter menos peças móveis, possui sistemas sofisticados que exigem revisões periódicas. Seguir o cronograma recomendado pela montadora é essencial para manter a eficiência e autonomia.",
        "Para preservar o desempenho e a vida útil da bateria do seu {make} {model} {year} elétrico, é fundamental seguir o cronograma de revisões estabelecido pela fabricante. A manutenção preventiva adequada garante economia e longevidade.",
        "A tecnologia avançada do {make} {model} {year} elétrico simplifica alguns aspectos da manutenção, mas introduz novos cuidados específicos. O cronograma de revisões foi desenvolvido para atender essas particularidades únicas.",
        "Manter um {make} {model} {year} elétrico em perfeito estado exige conhecimento especializado sobre sistemas de alta tensão e gerenciamento de energia. O cronograma preventivo protege esses componentes sofisticados.",
        // Novas
        "O {make} {model} {year} elétrico combina inovação e eficiência, mas exige cuidados técnicos específicos. O cronograma de revisões foca na manutenção de sistemas de alta tensão e na saúde da bateria.",
        "A engenharia avançada do {make} {model} {year} elétrico reduz a complexidade mecânica, mas demanda revisões especializadas. O cronograma garante a integridade dos componentes eletrônicos essenciais.",
        "A manutenção do {make} {model} {year} elétrico é otimizada para sua tecnologia de ponta. Seguir o cronograma de revisões assegura o funcionamento perfeito dos sistemas de propulsão elétrica.",
        "O {make} {model} {year} elétrico requer uma abordagem moderna de manutenção, com foco em diagnósticos eletrônicos avançados. O cronograma protege a longevidade dos componentes críticos.",
        "A complexidade reduzida do {make} {model} {year} elétrico é equilibrada por cuidados técnicos específicos. O cronograma de revisões mantém a confiabilidade dos sistemas de energia.",

        // Grupo 2: Econômicas e sustentabilidade (5 existentes + 5 novas)
        "A economia operacional do {make} {model} {year} elétrico se estende além do combustível, incluindo custos de manutenção reduzidos. Seguir o cronograma de revisões maximiza essas vantagens financeiras.",
        "Investir na manutenção preventiva do seu {make} {model} {year} elétrico protege um dos seus maiores ativos: a bateria de alta tensão. O cronograma adequado preserva essa tecnologia valiosa.",
        "O {make} {model} {year} elétrico representa uma escolha sustentável que se estende à manutenção mais limpa e eficiente. O cronograma de revisões mantém essa pegada ambiental reduzida.",
        "Proprietários conscientes do {make} {model} {year} elétrico entendem que a manutenção adequada multiplica os benefícios ambientais e econômicos da eletrificação.",
        "A revolução da mobilidade elétrica no {make} {model} {year} inclui uma abordagem diferenciada de manutenção. O cronograma preventivo garante que você aproveite todas as vantagens dessa tecnologia.",
        // Novas
        "O {make} {model} {year} elétrico oferece custos operacionais mais baixos, especialmente com manutenção adequada. O cronograma de revisões é projetado para maximizar essa economia.",
        "A sustentabilidade do {make} {model} {year} elétrico vai além da condução. O cronograma de manutenção garante uma operação mais limpa e eficiente a longo prazo.",
        "Escolher o {make} {model} {year} elétrico é investir em economia e responsabilidade ambiental. O cronograma de revisões preserva esses benefícios financeiros e ecológicos.",
        "A eficiência econômica do {make} {model} {year} elétrico depende de revisões regulares. O cronograma protege a bateria e reduz os custos de longo prazo.",
        "Manter o {make} {model} {year} elétrico com revisões programadas é uma decisão inteligente que amplifica suas vantagens econômicas e sustentáveis.",

        // Grupo 3: Performance e autonomia (5 existentes + 5 novas)
        "Manter a autonomia máxima do {make} {model} {year} elétrico depende de cuidados específicos com a bateria e sistemas de gerenciamento. O cronograma de revisões é essencial para preservar essas características.",
        "A performance silenciosa e eficiente do {make} {model} {year} elétrico se mantém através de verificações especializadas dos sistemas eletrônicos. Cada revisão preserva essa experiência única de condução.",
        "O {make} {model} {year} elétrico oferece aceleração instantânea e operação suave, características que dependem da manutenção adequada dos componentes elétricos e eletrônicos.",
        "Para garantir que seu {make} {model} {year} elétrico continue entregando a performance esperada, é crucial seguir o cronograma que considera as particularidades da propulsão elétrica.",
        "A eficiência energética do {make} {model} {year} elétrico está diretamente ligada ao estado de conservação de seus sistemas. O cronograma de revisões otimiza o consumo e a autonomia.",
        // Novas
        "A autonomia e potência do {make} {model} {year} elétrico são preservadas com revisões regulares. O cronograma foca na saúde da bateria e na eficiência dos sistemas.",
        "O {make} {model} {year} elétrico entrega uma condução dinâmica que depende de cuidados específicos. O cronograma de manutenção garante desempenho consistente.",
        "A experiência de condução do {make} {model} {year} elétrico é otimizada por revisões especializadas. O cronograma preserva a resposta rápida e a eficiência energética.",
        "Manter o {make} {model} {year} elétrico em condições ideais exige atenção à propulsão elétrica. O cronograma de revisões assegura autonomia e performance máximas.",
        "A eficiência do {make} {model} {year} elétrico depende de sistemas bem cuidados. O cronograma de manutenção protege a autonomia e a potência do veículo.",

        // Grupo 4: Segurança elétrica (4 existentes + 4 novas)
        "A segurança dos sistemas de alta tensão do {make} {model} {year} elétrico exige verificações especializadas que só profissionais qualificados podem realizar. O cronograma garante essas inspeções críticas.",
        "Dirigir com segurança no {make} {model} {year} elétrico significa manter todos os sistemas de proteção elétrica funcionando perfeitamente. As revisões programadas verificam esses componentes vitais.",
        "A tecnologia de alta tensão do {make} {model} {year} elétrico é segura quando adequadamente mantida. O cronograma de revisões inclui verificações específicas desses sistemas críticos.",
        "Proteger a família que utiliza o {make} {model} {year} elétrico requer atenção especial aos sistemas de segurança elétrica, verificados nas revisões programadas.",
        // Novas
        "A confiabilidade dos sistemas elétricos do {make} {model} {year} depende de revisões regulares. O cronograma garante a segurança dos componentes de alta tensão.",
        "O {make} {model} {year} elétrico prioriza segurança com tecnologia avançada. O cronograma de revisões assegura que os sistemas de proteção estejam sempre operacionais.",
        "Manter o {make} {model} {year} elétrico seguro exige cuidados especializados com alta tensão. O cronograma inclui verificações rigorosas para sua tranquilidade.",
        "A operação segura do {make} {model} {year} elétrico depende de manutenção preventiva. O cronograma foca na integridade dos sistemas elétricos críticos.",

        // Grupo 5: Tecnologia e futuro (4 existentes + 3 novas)
        "O {make} {model} {year} elétrico representa o futuro da mobilidade, com tecnologias que exigem cuidados específicos. O cronograma de manutenção preserva essa inovação tecnológica.",
        "A sofisticação eletrônica do {make} {model} {year} elétrico demanda revisões especializadas para manter todos os sistemas funcionando harmonicamente.",
        "Veículos elétricos como o {make} {model} {year} incorporam décadas de pesquisa em mobilidade sustentável. A manutenção adequada honra esse avanço tecnológico.",
        "A experiência premium de dirigir um {make} {model} {year} elétrico se mantém através de cuidados especializados com seus sistemas avançados.",
        // Novas
        "O {make} {model} {year} elétrico é um marco da inovação automotiva. O cronograma de revisões protege seus sistemas de última geração.",
        "A tecnologia de ponta do {make} {model} {year} elétrico exige manutenção precisa. O cronograma garante a continuidade dessa experiência futurista.",
        "O {make} {model} {year} elétrico incorpora avanços que revolucionam a mobilidade. O cronograma de manutenção preserva essa liderança tecnológica.",

        // Grupo 6: Praticidade e conveniência (4 existentes + 3 novas)
        "A simplicidade mecânica do {make} {model} {year} elétrico não elimina a necessidade de manutenção, mas a torna mais focada e eficiente. O cronograma reflete essa nova abordagem.",
        "Proprietários do {make} {model} {year} elétrico desfrutam de manutenção mais simples, mas igualmente importante. As revisões programadas garantem operação sem preocupações.",
        "O {make} {model} {year} elétrico oferece a conveniência de menos manutenção, mas os cuidados necessários são altamente especializados e cruciais para o funcionamento adequado.",
        "A revolução na experiência de propriedade do {make} {model} {year} elétrico inclui um cronograma de manutenção otimizado para os novos tempos da mobilidade.",
        // Novas
        "O {make} {model} {year} elétrico simplifica a manutenção, mas exige cuidados específicos. O cronograma oferece praticidade com revisões otimizadas.",
        "A experiência de possuir um {make} {model} {year} elétrico é descomplicada com um cronograma de manutenção eficiente e focado em tecnologia elétrica.",
        "O {make} {model} {year} elétrico combina praticidade e inovação. O cronograma de revisões torna a manutenção mais acessível e eficaz."
    ];

    // 50 variações de conclusão específicas para elétricos
    private array $conclusions = [
        // Grupo 1: Técnicas e especializadas (3 existentes + 6 novas)
        "Embora os veículos elétricos como o {make} {model} {year} geralmente exijam menos manutenção que os convencionais, seguir o cronograma de revisões é fundamental para preservar a vida útil da bateria e garantir a segurança dos sistemas de alta tensão. O investimento em manutenção preventiva especializada garante máxima eficiência e menor custo operacional a longo prazo.",
        "A manutenção do {make} {model} {year} elétrico representa uma nova era de cuidados automotivos, focada em sistemas eletrônicos avançados e gerenciamento energético. Seguir o cronograma especializado preserva a tecnologia e maximiza os benefícios da eletrificação.",
        "O cronograma de revisões do {make} {model} {year} elétrico foi desenvolvido para atender às necessidades específicas da propulsão elétrica. Cada verificação programada contribui para manter a eficiência, segurança e longevidade deste sistema avançado.",
        // Novas
        "A tecnologia avançada do {make} {model} {year} elétrico exige cuidados técnicos específicos. O cronograma de revisões garante que os sistemas de alta tensão e a bateria operem em condições ideais.",
        "Manter o {make} {model} {year} elétrico em perfeito estado depende de revisões especializadas. O cronograma protege os componentes eletrônicos e assegura desempenho confiável.",
        "O {make} {model} {year} elétrico combina simplicidade mecânica com sofisticação elétrica. O cronograma de manutenção foca em diagnósticos avançados para preservar essa inovação.",
        "A manutenção preventiva do {make} {model} {year} elétrico é essencial para proteger seus sistemas complexos. O cronograma garante a integridade da bateria e dos componentes de alta tensão.",
        "Seguir o cronograma de revisões do {make} {model} {year} elétrico assegura a longevidade dos sistemas eletrônicos, mantendo a eficiência e a confiabilidade da propulsão elétrica.",
        "O {make} {model} {year} elétrico requer uma abordagem técnica moderna. O cronograma de manutenção protege a tecnologia avançada e otimiza o desempenho do veículo.",

        // Grupo 2: Econômicas e valor (3 existentes + 6 novas)
        "O investimento em manutenção preventiva do {make} {model} {year} elétrico se paga através de maior durabilidade da bateria, eficiência energética mantida e custos operacionais reduzidos. O cronograma é a chave para maximizar o retorno do investimento em mobilidade elétrica.",
        "A economia operacional do {make} {model} {year} elétrico se estende por toda sua vida útil quando o cronograma de manutenção é respeitado. Cada revisão protege o patrimônio tecnológico e preserva as vantagens financeiras da eletrificação.",
        "Manter o {make} {model} {year} elétrico através do cronograma adequado é um investimento inteligente que preserva tanto o valor do veículo quanto seus benefícios econômicos únicos.",
        // Novas
        "O {make} {model} {year} elétrico oferece economia significativa, amplificada por revisões regulares. O cronograma de manutenção protege a bateria e reduz custos a longo prazo.",
        "A manutenção adequada do {make} {model} {year} elétrico garante economia operacional contínua. O cronograma preserva os benefícios financeiros da mobilidade elétrica.",
        "Investir no cronograma de revisões do {make} {model} {year} elétrico maximiza o valor do veículo, protegendo a bateria e otimizando os custos de operação.",
        "O {make} {model} {year} elétrico é um investimento em eficiência. O cronograma de manutenção assegura que os benefícios econômicos sejam mantidos ao longo do tempo.",
        "A economia de possuir um {make} {model} {year} elétrico é reforçada por revisões preventivas. O cronograma protege os componentes mais valiosos do veículo.",
        "Manter o {make} {model} {year} elétrico com revisões programadas é a chave para preservar suas vantagens financeiras e a durabilidade de seus sistemas.",

        // Grupo 3: Sustentabilidade e responsabilidade (3 existentes + 5 novas)
        "Escolher um {make} {model} {year} elétrico demonstra consciência ambiental que se estende à manutenção responsável. O cronograma de revisões mantém a pegada ecológica reduzida e a eficiência energética.",
        "A sustentabilidade do {make} {model} {year} elétrico depende não apenas de sua operação limpa, mas também de manutenção adequada que preserve essa característica ao longo dos anos.",
        "Manter o {make} {model} {year} elétrico adequadamente é contribuir para um futuro mais sustentável, preservando a tecnologia que representa a evolução da mobilidade urbana.",
        // Novas
        "O {make} {model} {year} elétrico é uma escolha sustentável que exige manutenção consciente. O cronograma de revisões preserva a eficiência e reduz o impacto ambiental.",
        "A operação ecológica do {make} {model} {year} elétrico é complementada por revisões regulares. O cronograma garante uma mobilidade mais limpa e eficiente.",
        "Manter o {make} {model} {year} elétrico com o cronograma adequado reforça seu compromisso com a sustentabilidade, protegendo a tecnologia verde do veículo.",
        "O {make} {model} {year} elétrico representa um passo rumo à mobilidade sustentável. O cronograma de manutenção preserva essa contribuição ambiental.",
        "A responsabilidade ambiental do {make} {model} {year} elétrico é mantida por revisões especializadas. O cronograma assegura operação limpa e eficiente.",

        // Grupo 4: Tecnologia e inovação (3 existentes + 5 novas)
        "A tecnologia avançada presente no {make} {model} {year} elétrico merece cuidados à altura de sua sofisticação. O cronograma de revisões preserva a inovação e garante que você continue desfrutando do melhor da mobilidade elétrica.",
        "O {make} {model} {year} elétrico incorpora décadas de pesquisa em mobilidade sustentável. A manutenção adequada honra esse avanço tecnológico e garante sua continuidade.",
        "Preservar a tecnologia de ponta do {make} {model} {year} elétrico através do cronograma adequado é investir no futuro da mobilidade e na satisfação duradoura com a escolha elétrica.",
        // Novas
        "O {make} {model} {year} elétrico é um marco da inovação automotiva. O cronograma de revisões protege seus sistemas avançados e mantém a liderança tecnológica.",
        "A sofisticação do {make} {model} {year} elétrico exige manutenção precisa. O cronograma garante que a tecnologia de ponta funcione harmoniosamente.",
        "O {make} {model} {year} elétrico representa o futuro da mobilidade. O cronograma de revisões preserva seus sistemas inovadores e a experiência de condução.",
        "Manter o {make} {model} {year} elétrico com revisões especializadas protege a inovação que define a mobilidade elétrica moderna.",
        "A tecnologia revolucionária do {make} {model} {year} elétrico é preservada por um cronograma de manutenção que mantém seus sistemas de última geração.",

        // Grupo 5: Segurança e confiabilidade (3 existentes + 5 novas)
        "A segurança dos sistemas de alta tensão do {make} {model} {year} elétrico depende fundamentalmente das verificações especializadas realizadas nas revisões programadas. Cada inspeção garante operação segura e confiável.",
        "Dirigir com tranquilidade no {make} {model} {year} elétrico significa confiar nos sistemas de proteção adequadamente mantidos. O cronograma de revisões é essencial para essa confiança.",
        "A confiabilidade silenciosa do {make} {model} {year} elétrico se mantém através de cuidados especializados que só o cronograma adequado pode proporcionar.",
        // Novas
        "O {make} {model} {year} elétrico prioriza segurança com sistemas de alta tensão. O cronograma de revisões garante que esses componentes estejam sempre operacionais.",
        "A operação segura do {make} {model} {year} elétrico depende de revisões regulares. O cronograma assegura a integridade dos sistemas elétricos críticos.",
        "Manter o {make} {model} {year} elétrico com revisões especializadas é essencial para a segurança. O cronograma protege os sistemas de alta tensão e a confiança do motorista.",
        "A confiabilidade do {make} {model} {year} elétrico é garantida por um cronograma de manutenção que inclui verificações rigorosas dos sistemas de segurança elétrica.",
        "O {make} {model} {year} elétrico oferece tranquilidade com revisões programadas. O cronograma mantém a segurança e a confiabilidade dos componentes elétricos.",

        // Grupo 6: Experiência e satisfação (3 existentes + 5 novas)
        "A experiência única de dirigir um {make} {model} {year} elétrico se preserva através de manutenção especializada que mantém todos os sistemas funcionando em harmonia perfeita.",
        "Proprietários satisfeitos do {make} {model} {year} elétrico compartilham o cuidado com a manutenção especializada. O cronograma é fundamental para manter essa satisfação ao longo dos anos.",
        "A revolução na experiência automotiva proporcionada pelo {make} {model} {year} elétrico se mantém através de revisões que preservam suas características únicas e vantagens exclusivas.",
        // Novas
        "A condução prazerosa do {make} {model} {year} elétrico é mantida por revisões especializadas. O cronograma garante uma experiência consistente e satisfatória.",
        "O {make} {model} {year} elétrico oferece uma experiência premium que depende de manutenção adequada. O cronograma preserva o prazer de dirigir.",
        "Proprietários do {make} {model} {year} elétrico desfrutam de uma experiência única com revisões regulares. O cronograma mantém a harmonia dos sistemas avançados.",
        "A satisfação de possuir um {make} {model} {year} elétrico é amplificada por um cronograma de manutenção que protege suas características exclusivas.",
        "O {make} {model} {year} elétrico redefine a condução moderna. O cronograma de revisões garante que essa experiência permaneça excepcional."
    ];

    // Variações de FAQ específicas para veículos elétricos
    private array $faqVariations = [
        'tecnico_eletrico' => [
            'battery_maintenance' => "Como devo cuidar da bateria do {make} {model}?",
            'charging_frequency' => 'Com que frequência devo carregar o veículo?',
            'range_degradation' => 'É normal a autonomia diminuir com o tempo?',
            'specialized_service' => 'Qualquer oficina pode fazer manutenção em veículos elétricos?',
            'warranty_battery' => 'Como funciona a garantia da bateria?'
        ],
        'pratico_eletrico' => [
            'maintenance_frequency' => "Com que frequência devo revisar meu {make} {model} elétrico?",
            'battery_lifespan' => 'Quanto tempo dura a bateria de um carro elétrico?',
            'charging_home' => 'Posso instalar carregador em casa?',
            'emergency_service' => 'O que fazer se o carro elétrico parar na estrada?'
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
            ['revisao' => '1ª Revisão', 'intervalo' => '10.000 km ou 12 meses', 'principais_servicos' => 'Verificação sistema elétrico, freios, conectores', 'estimativa_custo' => $this->getCostRange($vehicleData, 1)],
            ['revisao' => '2ª Revisão', 'intervalo' => '20.000 km ou 24 meses', 'principais_servicos' => 'Diagnóstico bateria, freios regenerativos, filtros', 'estimativa_custo' => $this->getCostRange($vehicleData, 2)],
            ['revisao' => '3ª Revisão', 'intervalo' => '30.000 km ou 36 meses', 'principais_servicos' => 'Sistema térmico, alta tensão, fluido freio', 'estimativa_custo' => $this->getCostRange($vehicleData, 3)],
            ['revisao' => '4ª Revisão', 'intervalo' => '40.000 km ou 48 meses', 'principais_servicos' => 'Propulsão elétrica, refrigeração, software', 'estimativa_custo' => $this->getCostRange($vehicleData, 4)],
            ['revisao' => '5ª Revisão', 'intervalo' => '50.000 km ou 60 meses', 'principais_servicos' => 'Análise completa bateria, sistemas auxiliares', 'estimativa_custo' => $this->getCostRange($vehicleData, 5)],
            ['revisao' => '6ª Revisão', 'intervalo' => '60.000 km ou 72 meses', 'principais_servicos' => 'Revisão ampla, atualizações, análise capacidade', 'estimativa_custo' => $this->getCostRange($vehicleData, 6)]
        ];
    }

    public function generateDetailedSchedule(array $vehicleData): array
    {
        $style = $this->selectMaintenanceStyle($vehicleData);
        $schedule = [];
        $revisionNumber = 1;

        foreach ($this->maintenanceSchedule as $interval => $serviceKeys) {
            preg_match('/(\d+\.?\d*)\s*km/', $interval, $matches);
            $km = $matches[1] ?? '0';

            $services = $this->translateServices($serviceKeys, $style);

            $schedule[] = [
                'numero_revisao' => $revisionNumber,
                'intervalo' => $interval,
                'km' => $km,
                'servicos_principais' => array_slice($services, 0, 4),
                'verificacoes_complementares' => array_slice($services, 4),
                'estimativa_custo' => $this->getCostRange($vehicleData, $revisionNumber),
                'observacoes' => $this->getVariedObservation($revisionNumber, $vehicleData)
            ];

            $revisionNumber++;
        }

        return $schedule;
    }

    public function generatePreventiveMaintenance(array $vehicleData): array
    {
        return [
            'verificacoes_mensais' => [
                'Verificar nível de carga da bateria',
                'Conferir calibragem dos pneus',
                'Testar luzes e sistemas eletrônicos',
                'Limpar conectores de carregamento'
            ],
            'verificacoes_trimestrais' => [
                'Fluido de freio',
                'Sistemas de segurança eletrônicos',
                'Desgaste dos pneus',
                'Funcionamento do ar-condicionado'
            ],
            'cuidados_especiais' => [
                'Evitar descargas completas da bateria',
                'Utilizar carregadores homologados',
                'Proteger conectores da umidade',
                'Monitorar temperatura da bateria'
            ]
        ];
    }

    public function generateCriticalParts(array $vehicleData): array
    {
        return [
            'Bateria de alta tensão' => 'Componente mais crítico, requer verificações especializadas',
            'Sistema de refrigeração da bateria' => 'Fundamental para longevidade da bateria',
            'Conectores de alta tensão' => 'Devem ser mantidos limpos e bem conectados'
        ];
    }

    public function generateTechnicalSpecs(array $vehicleData): array
    {
        return [
            'capacidade_bateria' => $this->getBatteryCapacity($vehicleData),
            'autonomia_estimada' => $this->getEstimatedRange($vehicleData),
            'tipo_carregamento' => 'AC/DC - Consulte manual do proprietário',
            'pressao_pneus' => $this->getTirePressure($vehicleData),
            'fluidos_necessarios' => 'Fluido de freio, líquido de arrefecimento',
            'sistema_propulsao' => 'Motor elétrico - sem necessidade de óleo',
            'garantia_bateria' => $this->getBatteryWarranty($vehicleData)
        ];
    }

    public function generateWarrantyInfo(array $vehicleData): array
    {
        return [
            'prazo_garantia_geral' => '3 anos ou 100.000 km',
            'garantia_bateria' => '8 anos ou 160.000 km (para capacidade acima de 70%)',
            'garantia_motor_eletrico' => '8 anos ou 160.000 km',
            'observacoes_importantes' => 'Manutenção deve ser realizada em concessionária autorizada para veículos elétricos',
            'dicas_preservacao' => [
                'Evitar exposição a temperaturas extremas',
                'Usar carregadores homologados',
                'Seguir cronograma de revisões'
            ]
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

        // Agrupar veículos elétricos por faixas de ano e marca
        $yearGroup = floor($year / 3) * 3;
        $segment = $this->getElectricSegment($vehicleData);

        return "electric_{$make}_{$segment}_{$yearGroup}";
    }

    private function getElectricSegment(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        $model = strtolower($vehicleData['model'] ?? '');

        // Classificar por segmento elétrico
        if (in_array($make, ['tesla', 'porsche', 'audi', 'bmw', 'mercedes-benz'])) {
            return 'premium';
        } elseif (strpos($model, 'suv') !== false || strpos($model, 'x') !== false) {
            return 'suv_eletrico';
        } elseif (in_array($make, ['nissan', 'chevrolet', 'byd', 'jac'])) {
            return 'popular';
        }

        return 'geral';
    }

    private function selectMaintenanceStyle(array $vehicleData): string
    {
        $segment = $this->getElectricSegment($vehicleData);
        $vehicleKey = $this->getVehicleKey($vehicleData);

        $usedStyles = self::$usedMaintenanceStyles[$vehicleKey] ?? [];
        $availableStyles = array_diff(array_keys($this->maintenanceStyles), $usedStyles);

        if (empty($availableStyles)) {
            self::$usedMaintenanceStyles[$vehicleKey] = [];
            $availableStyles = array_keys($this->maintenanceStyles);
        }

        // CORREÇÃO: Verificação adicional para garantir que availableStyles não está vazio
        if (empty($availableStyles)) {
            $availableStyles = ['tecnico_eletrico']; // Fallback seguro
        }

        // Preferência por segmento
        $preferredStyles = [
            'premium' => ['premium_eletrico', 'detalhado_eletrico'],
            'popular' => ['simples_eletrico', 'tecnico_eletrico'],
            'suv_eletrico' => ['detalhado_eletrico', 'tecnico_eletrico']
        ];

        $preferred = $preferredStyles[$segment] ?? ['tecnico_eletrico'];
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
        $styleData = $this->maintenanceStyles[$style] ?? $this->maintenanceStyles['tecnico_eletrico'];
        $services = [];

        foreach ($serviceKeys as $key) {
            if (isset($styleData[$key])) {
                $services[] = $styleData[$key];
            } else {
                $services[] = $this->getElectricServiceFallback($key);
            }
        }

        return $services;
    }

    private function getElectricServiceFallback(string $serviceKey): string
    {
        $fallbacks = [
            'brake_check' => 'Verificação do sistema de freios',
            'ac_filter' => 'Substituição do filtro de ar-condicionado',
            'electrical_basic' => 'Verificação básica dos sistemas elétricos',
            'tire_check' => 'Inspeção dos pneus',
            'brake_fluid_check' => 'Verificação do fluido de freio',
            'suspension_check' => 'Inspeção da suspensão',
            'electrical_full' => 'Verificação completa dos sistemas elétricos',
            'tire_rotation' => 'Rodízio dos pneus',
            'cooling_check' => 'Verificação do sistema de refrigeração',
            'battery_thermal' => 'Sistema de gerenciamento térmico da bateria',
            'high_voltage_connectors' => 'Inspeção dos conectores de alta tensão',
            'auxiliary_systems' => 'Verificação dos sistemas auxiliares',
            'brake_fluid_change' => 'Troca do fluido de freio',
            'hoses_check' => 'Verificação de mangueiras',
            'alignment_check' => 'Verificação do alinhamento',
            'propulsion_system' => 'Verificação do sistema de propulsão elétrica',
            'cooling_battery_fluid' => 'Verificação do fluido de refrigeração da bateria',
            'suspension_full' => 'Inspeção completa da suspensão',
            'brake_regen_full' => 'Verificação dos freios regenerativos',
            'battery_advanced' => 'Diagnóstico avançado da bateria',
            'connectivity_check' => 'Verificação dos sistemas de conectividade',
            'battery_full_analysis' => 'Análise completa da bateria',
            'cooling_system_complete' => 'Verificação completa do sistema de refrigeração',
            'complete_revision' => 'Revisão ampla de todos os sistemas'
        ];

        // CORREÇÃO: Fallback final para evitar erros
        return $fallbacks[$serviceKey] ?? 'Verificação especializada para veículos elétricos';
    }

    private function getCostRange(array $vehicleData, int $revisionNumber): string
    {
        return match ($revisionNumber) {
            1 => 'R$ 280 - R$ 350',
            2 => 'R$ 320 - R$ 400',
            3 => 'R$ 450 - R$ 550',
            4 => 'R$ 520 - R$ 650',
            5 => 'R$ 400 - R$ 500',
            6 => 'R$ 650 - R$ 800',
            default => 'R$ 400 - R$ 600'
        };
    }

    private function getVariedObservation(int $revisionNumber, array $vehicleData): string
    {
        $observations = [
            1 => 'Primeira revisão focada em adaptação do veículo elétrico',
            2 => 'Verificação dos sistemas após período inicial de uso',
            3 => 'Revisão intermediária com foco na bateria e sistemas térmicos',
            4 => 'Verificação avançada dos sistemas de propulsão',
            5 => 'Análise detalhada da degradação da bateria',
            6 => 'Revisão completa com análise de capacidade e performance'
        ];

        return $observations[$revisionNumber] ?? 'Revisão padrão conforme cronograma do fabricante';
    }

    private function getFAQStyle(string $vehicleKey): string
    {
        $usedStyles = self::$usedFAQStyles[$vehicleKey] ?? [];
        $availableStyles = array_diff(array_keys($this->faqVariations), $usedStyles);

        if (empty($availableStyles)) {
            self::$usedFAQStyles[$vehicleKey] = [];
            $availableStyles = array_keys($this->faqVariations);
        }

        $selectedStyle = $availableStyles[array_rand($availableStyles)];

        if (!isset(self::$usedFAQStyles[$vehicleKey])) {
            self::$usedFAQStyles[$vehicleKey] = [];
        }
        self::$usedFAQStyles[$vehicleKey][] = $selectedStyle;

        return $selectedStyle;
    }

    private function getFAQAnswer(string $key, array $vehicleData, string $style): string
    {
        $make = $vehicleData['make'] ?? '';
        $model = $vehicleData['model'] ?? '';

        return match ($key) {
            'battery_maintenance' => "Para manter a bateria do {$make} {$model} em bom estado, evite descargas completas, utilize carregadores homologados e mantenha o veículo em temperaturas moderadas sempre que possível.",

            'charging_frequency' => "Não é necessário aguardar a bateria descarregar completamente. Carregue sempre que conveniente, mantendo preferencialmente entre 20% e 80% de carga para uso diário.",

            'range_degradation' => "É normal haver uma pequena redução da autonomia ao longo dos anos. Baterias modernas mantêm cerca de 80% da capacidade original após 8 anos ou 160.000 km.",

            'specialized_service' => "Veículos elétricos requerem técnicos especializados e equipamentos específicos. Sempre procure concessionárias autorizadas ou oficinas certificadas para manutenção.",

            'warranty_battery' => "A bateria possui garantia específica, geralmente de 8 anos ou 160.000 km, cobrindo degradação abaixo de 70% da capacidade original. Consulte o manual para detalhes.",

            'maintenance_frequency' => "Veículos elétricos geralmente requerem menos manutenção que veículos convencionais. Siga o cronograma do fabricante, tipicamente a cada 10.000 km ou 12 meses.",

            'battery_lifespan' => "Baterias de veículos elétricos modernos duram entre 8 a 15 anos, dependendo do uso e cuidados. A tecnologia atual oferece alta durabilidade.",

            'charging_home' => "Sim, é possível instalar carregadores residenciais. Consulte um eletricista qualificado e verifique a capacidade da sua instalação elétrica.",

            'emergency_service' => "Em caso de pane, contacte o serviço de emergência da montadora. Muitos problemas podem ser diagnosticados remotamente. Evite tentar reparos por conta própria.",

            default => "Consulte sempre o manual do proprietário e a concessionária autorizada para informações específicas sobre seu {$make} {$model}."
        };
    }

    private function getBatteryCapacity(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        $model = strtolower($vehicleData['model'] ?? '');

        // Estimativas baseadas em modelos conhecidos
        if ($make === 'tesla') {
            return '75-100 kWh (varia por versão)';
        } elseif ($make === 'nissan' && strpos($model, 'leaf') !== false) {
            return '40-62 kWh';
        } elseif ($make === 'chevrolet' && strpos($model, 'bolt') !== false) {
            return '65 kWh';
        } elseif ($make === 'byd') {
            return '50-80 kWh (varia por modelo)';
        }

        return 'Consulte manual do proprietário';
    }

    private function getEstimatedRange(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        $model = strtolower($vehicleData['model'] ?? '');

        // Estimativas baseadas em modelos conhecidos
        if ($make === 'tesla') {
            return '400-600 km (varia por versão)';
        } elseif ($make === 'nissan' && strpos($model, 'leaf') !== false) {
            return '270-385 km';
        } elseif ($make === 'chevrolet' && strpos($model, 'bolt') !== false) {
            return '380 km';
        } elseif ($make === 'byd') {
            return '300-500 km (varia por modelo)';
        }

        return 'Consulte especificações do fabricante';
    }

    private function getTirePressure(array $vehicleData): string
    {
        $emptyFront = $vehicleData['pressure_empty_front'] ?? 32;
        $emptyRear = $vehicleData['pressure_empty_rear'] ?? 32;

        return "Dianteiros: {$emptyFront} PSI | Traseiros: {$emptyRear} PSI (veículo vazio)";
    }

    private function getBatteryWarranty(array $vehicleData): string
    {
        return '8 anos ou 160.000 km (capacidade mínima 70%)';
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

    /**
     * Método para verificar se há conflito de estado
     */
    public function getStateDebugInfo(): array
    {
        return [
            'used_intros_count' => array_sum(array_map('count', self::$usedIntros)),
            'used_conclusions_count' => array_sum(array_map('count', self::$usedConclusions)),
            'used_maintenance_styles_count' => array_sum(array_map('count', self::$usedMaintenanceStyles)),
            'used_faq_styles_count' => array_sum(array_map('count', self::$usedFAQStyles)),
            'total_vehicle_keys' => count(array_unique(array_merge(
                array_keys(self::$usedIntros),
                array_keys(self::$usedConclusions),
                array_keys(self::$usedMaintenanceStyles),
                array_keys(self::$usedFAQStyles)
            )))
        ];
    }

    private function replacePlaceholders(string $text, array $vehicleData): string
    {
        return str_replace(
            ['{make}', '{model}', '{year}'],
            [$vehicleData['make'] ?? '', $vehicleData['model'] ?? '', $vehicleData['year'] ?? ''],
            $text
        );
    }
}
