<?php

namespace Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates;

class MotorcycleMaintenanceTemplate
{
    // Sistema de controle de variações usadas
    private static array $usedIntros = [];
    private static array $usedConclusions = [];
    private static array $usedMaintenanceStyles = [];
    private static array $usedFAQStyles = [];

    // 25 variações de introdução específicas para motocicletas
    private array $intros = [
        // Grupo 1: Técnicas e especializadas
        "Manter a sua {make} {model} {year} em dia com as revisões é fundamental para garantir sua durabilidade, segurança e desempenho. O cronograma recomendado pela montadora foi desenvolvido especificamente para as necessidades da sua motocicleta.",
        "A {make} {model} {year} exige cuidados periódicos para continuar oferecendo a melhor performance e segurança. Conhecer o cronograma de revisões ajuda a planejar a manutenção e evitar surpresas desagradáveis.",
        "Para garantir a longevidade da sua {make} {model} {year}, é essencial seguir o cronograma de revisões estabelecido pela fabricante. A manutenção preventiva adequada protege seu investimento e evita problemas futuros.",
        "A engenharia da {make} {model} {year} foi desenvolvida para oferecer máxima performance e confiabilidade. Seguir o cronograma de revisões preserva essas características e garante pilotagem segura.",
        "Manter sua {make} {model} {year} em perfeito estado exige conhecimento específico sobre sistemas motociclísticos. O cronograma de revisões garante que todos os componentes críticos sejam verificados adequadamente.",

        // Grupo 2: Segurança e proteção
        "A segurança na pilotagem da {make} {model} {year} depende diretamente do cumprimento rigoroso do cronograma de revisões. Cada verificação programada protege você e outros usuários da via.",
        "Pilotar com segurança e confiança na sua {make} {model} {year} significa manter todos os sistemas em perfeito estado. O cronograma de revisões é seu aliado nessa responsabilidade.",
        "A {make} {model} {year} é sua companheira de estrada, e cuidar dela adequadamente garante viagens seguras e prazerosas. As revisões programadas são fundamentais para essa confiança.",
        "Proteger sua vida e a de outros motociclistas começa com a manutenção adequada da {make} {model} {year}. O cronograma de revisões é essencial para essa responsabilidade.",
        "A liberdade de pilotar a {make} {model} {year} vem acompanhada da responsabilidade de mantê-la segura. Seguir o cronograma de revisões é demonstrar esse compromisso.",

        // Grupo 3: Performance e paixão
        "A paixão por pilotar a {make} {model} {year} se mantém viva através de cuidados que preservem sua performance e características originais. Cada revisão renova essa experiência única.",
        "O prazer de acelerar na {make} {model} {year} depende de sistemas bem ajustados e componentes em perfeito estado. O cronograma de revisões preserva essa emoção.",
        "A {make} {model} {year} foi criada para proporcionar experiências únicas de pilotagem. Manter essa essência exige seguir rigorosamente o cronograma de manutenção preventiva.",
        "Cada quilômetro pilotado na {make} {model} {year} deve ser uma experiência prazerosa e segura. As revisões programadas garantem que essa satisfação se mantenha ao longo dos anos.",
        "A performance característica da {make} {model} {year} se preserva através de ajustes precisos e verificações especializadas presentes no cronograma de revisões.",

        // Grupo 4: Economia e investimento
        "Investir na manutenção preventiva da sua {make} {model} {year} é muito mais econômico que lidar com reparos emergenciais. O cronograma de revisões maximiza o retorno do seu investimento.",
        "A economia na operação da {make} {model} {year} vai além do combustível, incluindo manutenção adequada que previne gastos desnecessários com reparos.",
        "Proteger o patrimônio representado pela {make} {model} {year} significa seguir o cronograma de revisões que preserva tanto seu funcionamento quanto seu valor.",
        "O custo-benefício da manutenção preventiva da {make} {model} {year} fica evidente quando comparamos os valores das revisões com os gastos de reparos por negligência.",
        "Proprietários experientes da {make} {model} {year} sabem que seguir o cronograma de revisões é um investimento inteligente que se paga através de menor incidência de problemas.",

        // Grupo 5: Experiência e tradição
        "A tradição motociclística da {make} na {model} {year} se mantém através de cuidados que honram a engenharia e qualidade desta marca reconhecida mundialmente.",
        "Escolher uma {make} {model} {year} demonstra conhecimento motociclístico. Manter essa escolha através de revisões adequadas preserva a experiência que motivou sua decisão.",
        "A cultura motociclística valoriza tanto a máquina quanto os cuidados com ela. A {make} {model} {year} merece a manutenção que sua engenharia exige.",
        "Fazer parte da família {make} significa compartilhar valores de qualidade e cuidado. A {model} {year} representa esses princípios e merece manutenção à altura.",

        // Grupo 6: Praticidade e conveniência
        "A {make} {model} {year} é sua companheira diária, e mantê-la funcionando perfeitamente facilita sua rotina e garante mobilidade sem preocupações.",
        "Ter uma {make} {model} {year} sempre pronta para uso requer disciplina na manutenção preventiva. O cronograma de revisões é seu guia para essa confiabilidade.",
        "A versatilidade da {make} {model} {year} se mantém através de cuidados preventivos que garantem disponibilidade quando você mais precisa dela."
    ];

    // 18 variações de conclusão específicas para motocicletas
    private array $conclusions = [
        // Grupo 1: Segurança e responsabilidade
        "Seguir o cronograma de revisões da sua {make} {model} {year} é fundamental para manter sua segurança, desempenho e durabilidade. Motocicletas exigem cuidados mais frequentes que automóveis, mas esse investimento em manutenção preventiva garante muitos quilômetros de pilotagem segura e prazerosa.",
        "A manutenção adequada da {make} {model} {year} é uma questão de segurança que vai além do piloto, impactando todos que compartilham as vias. Cada revisão é um investimento na segurança coletiva.",
        "A responsabilidade de pilotar uma {make} {model} {year} inclui mantê-la em perfeitas condições. O cronograma de revisões é fundamental para honrar essa responsabilidade.",
        "Cada revisão da {make} {model} {year} é um investimento na sua segurança e na preservação da paixão por pilotar. Manter o cronograma em dia é demonstrar amor pela motocicleta.",

        // Grupo 2: Performance e experiência
        "A experiência única de pilotar uma {make} {model} {year} se preserva através de manutenção que mantém todos os sistemas funcionando em harmonia perfeita.",
        "O prazer de acelerar na {make} {model} {year} depende de componentes bem ajustados e sistemas em perfeito estado. O cronograma de revisões preserva essa emoção a cada pilotada.",
        "A performance característica da {make} {model} {year} se mantém através de cuidados especializados que só o cronograma adequado pode proporcionar.",
        "Preservar a essência da {make} {model} {year} significa seguir o cronograma que mantém suas características originais e performance otimizada.",

        // Grupo 3: Economia e valor
        "O investimento em manutenção preventiva da {make} {model} {year} se paga através de maior confiabilidade, menor incidência de problemas e preservação do valor patrimonial.",
        "A economia operacional da {make} {model} {year} se estende por toda sua vida útil quando o cronograma de manutenção é respeitado rigorosamente.",
        "Proteger o valor da {make} {model} {year} é tão importante quanto garantir seu funcionamento. O cronograma de revisões contribui para ambos os objetivos.",
        "O {make} {model} {year} bem mantida é sinônimo de bom investimento. O cronograma de revisões maximiza tanto o desempenho quanto o valor patrimonial.",

        // Grupo 4: Confiabilidade e tranquilidade
        "A confiabilidade da {make} {model} {year} nas estradas depende fundamentalmente do cumprimento do cronograma de revisões especializadas.",
        "Pilotar com tranquilidade na {make} {model} {year} significa confiar nos sistemas adequadamente mantidos através das revisões programadas.",
        "A {make} {model} {year} oferece a liberdade da estrada quando adequadamente mantida. O cronograma de revisões garante essa confiança.",

        // Grupo 5: Tradição e cultura motociclística
        "A cultura motociclística valoriza tanto a máquina quanto os cuidados com ela. Manter a {make} {model} {year} adequadamente é honrar essa tradição.",
        "Fazer parte da família {make} significa compartilhar valores de qualidade e cuidado. A manutenção da {model} {year} reflete esses princípios.",
        "A tradição de excelência da {make} na {model} {year} se preserva através de manutenção que honra a engenharia e qualidade desta marca reconhecida."
    ];

    // Estilos de manutenção para motocicletas - EXPANDIDO
    private array $maintenanceStyles = [
        'tecnico_moto' => [
            'oil_change' => 'Substituição do óleo lubrificante e elemento filtrante conforme especificação técnica',
            'chain_maintenance' => 'Verificação e ajuste da tensão da corrente de transmissão com lubrificação',
            'brake_inspection' => 'Inspeção do sistema de frenagem e verificação de desgaste das pastilhas',
            'valve_clearance' => 'Verificação e ajuste das folgas das válvulas conforme especificação',
            'suspension_check' => 'Inspeção dos componentes da suspensão dianteira e traseira',
            'electrical_diag' => 'Diagnóstico eletrônico dos sistemas de ignição e carga',
            // ADICIONADO: Serviços faltantes
            'initial_revision' => 'Primeira revisão com verificação de assentamento dos componentes',
            'cables_adjustment' => 'Ajuste de cabos e comandos conforme especificação',
            'fluid_levels' => 'Verificação de níveis de fluidos operacionais',
            'cable_lubrication' => 'Lubrificação de cabos e articulações de controle',
            'electrical_check' => 'Verificação básica do sistema elétrico',
            'spark_plugs_check' => 'Verificação do estado das velas de ignição',
            'air_filter' => 'Substituição do filtro de ar do motor',
            'clutch_check' => 'Inspeção do sistema de embreagem',
            'electrical_full' => 'Diagnóstico completo do sistema elétrico',
            'injection_clean' => 'Limpeza do sistema de injeção eletrônica',
            'fuel_system' => 'Verificação do sistema de alimentação de combustível',
            'wheel_bearings' => 'Verificação dos rolamentos das rodas',
            'steering_check' => 'Inspeção do sistema de direção',
            'brake_fluid_change' => 'Troca do fluido de freio',
            'cooling_system' => 'Verificação do sistema de arrefecimento',
            'complete_revision' => 'Revisão completa de todos os sistemas',
            'spark_plugs_change' => 'Substituição das velas de ignição',
            'timing_check' => 'Verificação da sincronização do motor',
            'hoses_inspection' => 'Inspeção de mangueiras e tubulações',
            'brake_system_full' => 'Verificação completa do sistema de freios',
            'battery_check' => 'Verificação da bateria e sistema de carga',
            'transmission_check' => 'Verificação do sistema de transmissão',
            'exhaust_system' => 'Inspeção do sistema de escapamento',
            'suspension_full' => 'Inspeção completa da suspensão',
            'bearing_lubrication' => 'Lubrificação de rolamentos e articulações'
        ],
        'simples_moto' => [
            'oil_change' => 'Troca de óleo e filtro',
            'chain_maintenance' => 'Corrente (tensão e lubrificação)',
            'brake_inspection' => 'Verificação dos freios',
            'valve_clearance' => 'Folgas das válvulas',
            'suspension_check' => 'Suspensão',
            'electrical_diag' => 'Sistema elétrico',
            // ADICIONADO: Serviços faltantes versão simples
            'initial_revision' => 'Primeira revisão',
            'cables_adjustment' => 'Ajuste de cabos',
            'fluid_levels' => 'Níveis de fluidos',
            'cable_lubrication' => 'Lubrificação cabos',
            'electrical_check' => 'Elétrica básica',
            'spark_plugs_check' => 'Velas',
            'air_filter' => 'Filtro de ar',
            'clutch_check' => 'Embreagem',
            'electrical_full' => 'Sistema elétrico completo',
            'injection_clean' => 'Injeção',
            'fuel_system' => 'Sistema combustível',
            'wheel_bearings' => 'Rolamentos rodas',
            'steering_check' => 'Direção',
            'brake_fluid_change' => 'Fluido freio',
            'cooling_system' => 'Arrefecimento',
            'complete_revision' => 'Revisão completa',
            'spark_plugs_change' => 'Troca velas',
            'timing_check' => 'Sincronização',
            'hoses_inspection' => 'Mangueiras',
            'brake_system_full' => 'Freios completo',
            'battery_check' => 'Bateria',
            'transmission_check' => 'Transmissão',
            'exhaust_system' => 'Escapamento',
            'suspension_full' => 'Suspensão completa',
            'bearing_lubrication' => 'Lubrificação geral'
        ],
        'detalhado_moto' => [
            'oil_change' => 'Drenagem completa e reposição do óleo lubrificante com filtro original homologado',
            'chain_maintenance' => 'Inspeção minuciosa, tensionamento preciso e lubrificação da corrente de transmissão',
            'brake_inspection' => 'Verificação completa do sistema de freios incluindo pastilhas, discos e fluido',
            'valve_clearance' => 'Medição e ajuste preciso das folgas das válvulas conforme tolerâncias de fábrica',
            'suspension_check' => 'Análise completa dos componentes de suspensão e ajustes de regulagem',
            'electrical_diag' => 'Diagnóstico eletrônico avançado dos sistemas de ignição, carga e injeção',
            // ADICIONADO: Versão detalhada dos serviços faltantes
            'initial_revision' => 'Primeira revisão detalhada com verificação minuciosa do assentamento de componentes',
            'cables_adjustment' => 'Ajuste preciso de cabos e comandos com lubrificação das articulações',
            'fluid_levels' => 'Verificação criteriosa de níveis e qualidade de todos os fluidos',
            'cable_lubrication' => 'Lubrificação completa de cabos e articulações com produtos específicos',
            'electrical_check' => 'Verificação detalhada do sistema elétrico de baixa tensão',
            'spark_plugs_check' => 'Inspeção minuciosa das velas incluindo eletrodo e isolador',
            'air_filter' => 'Substituição do filtro de ar com limpeza do alojamento',
            'clutch_check' => 'Inspeção completa do sistema de embreagem e ajuste',
            'electrical_full' => 'Diagnóstico eletrônico completo de todos os sistemas',
            'injection_clean' => 'Limpeza especializada do sistema de injeção eletrônica',
            'fuel_system' => 'Verificação completa do sistema de alimentação e filtros',
            'wheel_bearings' => 'Inspeção detalhada dos rolamentos com verificação de folgas',
            'steering_check' => 'Inspeção completa do sistema de direção e alinhamento',
            'brake_fluid_change' => 'Troca completa do fluido de freio com sangria do sistema',
            'cooling_system' => 'Verificação completa do sistema de arrefecimento e termostato',
            'complete_revision' => 'Revisão ampla e detalhada de todos os sistemas da motocicleta',
            'spark_plugs_change' => 'Substituição das velas com verificação do sistema de ignição',
            'timing_check' => 'Verificação precisa da sincronização e ponto de ignição',
            'hoses_inspection' => 'Inspeção detalhada de mangueiras, abraçadeiras e conexões',
            'brake_system_full' => 'Verificação completa incluindo discos, pastilhas e sistema hidráulico',
            'battery_check' => 'Teste completo da bateria, alternador e sistema de carga',
            'transmission_check' => 'Verificação completa da transmissão e embreagem',
            'exhaust_system' => 'Inspeção completa do sistema de escapamento e fixações',
            'suspension_full' => 'Inspeção completa da suspensão com ajustes de regulagem',
            'bearing_lubrication' => 'Lubrificação completa de todos os rolamentos e articulações'
        ],
        'performance_moto' => [
            'oil_change' => 'Substituição com óleo de alta performance e filtro premium para máximo rendimento',
            'chain_maintenance' => 'Ajuste de precisão da corrente com lubrificante de alta qualidade para performance',
            'brake_inspection' => 'Verificação especializada do sistema de freios para pilotagem esportiva',
            'valve_clearance' => 'Ajuste de precisão das válvulas para otimização da performance do motor',
            'suspension_check' => 'Configuração especializada da suspensão para características de pilotagem',
            'electrical_diag' => 'Diagnóstico avançado e otimização dos sistemas eletrônicos de performance',
            // ADICIONADO: Versão performance dos serviços faltantes
            'initial_revision' => 'Primeira revisão especializada com foco em performance e rodagem',
            'cables_adjustment' => 'Ajuste de precisão dos cabos para resposta otimizada',
            'fluid_levels' => 'Verificação com fluidos de alta performance',
            'cable_lubrication' => 'Lubrificação com produtos premium para performance',
            'electrical_check' => 'Verificação do sistema elétrico com foco em performance',
            'spark_plugs_check' => 'Verificação de velas de alta performance',
            'air_filter' => 'Filtro de ar de alta fluxo para performance',
            'clutch_check' => 'Inspeção da embreagem para pilotagem esportiva',
            'electrical_full' => 'Diagnóstico eletrônico avançado para otimização',
            'injection_clean' => 'Limpeza especializada para máxima eficiência',
            'fuel_system' => 'Otimização do sistema de combustível',
            'wheel_bearings' => 'Rolamentos de precisão para performance',
            'steering_check' => 'Configuração da direção para pilotagem esportiva',
            'brake_fluid_change' => 'Fluido de freio de alta performance',
            'cooling_system' => 'Otimização do sistema de arrefecimento',
            'complete_revision' => 'Revisão completa com foco em performance máxima',
            'spark_plugs_change' => 'Velas de alta performance para máximo rendimento',
            'timing_check' => 'Sincronização otimizada para performance',
            'hoses_inspection' => 'Mangueiras de alta temperatura para performance',
            'brake_system_full' => 'Sistema de freios de alta performance',
            'battery_check' => 'Sistema de carga otimizado',
            'transmission_check' => 'Transmissão configurada para performance',
            'exhaust_system' => 'Sistema de escape para máxima performance',
            'suspension_full' => 'Suspensão configurada para pilotagem esportiva',
            'bearing_lubrication' => 'Lubrificação premium para alta performance'
        ]
    ];

    // Cronograma base para motocicletas
    private array $maintenanceSchedule = [
        '1.000 km ou 6 meses' => [
            'initial_revision',
            'oil_change',
            'chain_maintenance',
            'valve_clearance',
            'brake_inspection',
            'cables_adjustment',
            'electrical_diag'
        ],
        '5.000 km ou 12 meses' => [
            'oil_change',
            'brake_inspection',
            'fluid_levels',
            'cable_lubrication',
            'chain_maintenance',
            'electrical_check',
            'spark_plugs_check'
        ],
        '10.000 km ou 18 meses' => [
            'oil_change',
            'air_filter',
            'valve_clearance',
            'clutch_check',
            'electrical_full',
            'injection_clean',
            'suspension_check'
        ],
        '15.000 km ou 24 meses' => [
            'oil_change',
            'spark_plugs_check',
            'fuel_system',
            'wheel_bearings',
            'steering_check',
            'brake_fluid_change',
            'cooling_system'
        ],
        '20.000 km ou 30 meses' => [
            'complete_revision',
            'oil_change',
            'spark_plugs_change',
            'timing_check',
            'hoses_inspection',
            'brake_system_full',
            'battery_check'
        ],
        '25.000 km ou 36 meses' => [
            'oil_change',
            'air_filter',
            'transmission_check',
            'valve_clearance',
            'exhaust_system',
            'suspension_full',
            'bearing_lubrication'
        ]
    ];

    // Variações de FAQs para motocicletas
    private array $faqVariations = [
        'basico_moto' => [
            'maintenance_frequency' => "Com que frequência devo fazer as revisões da minha {make} {model}?",
            'dealership_required' => 'Posso fazer as revisões da minha moto fora da concessionária?',
            'first_revision' => 'É verdade que devo fazer a primeira revisão aos 1.000 km?',
            'chain_lubrication' => 'Com que frequência devo lubrificar a corrente?'
        ],
        'completo_moto' => [
            'maintenance_frequency' => "Qual o intervalo correto de revisões para a {make} {model}?",
            'dealership_required' => 'As revisões precisam ser feitas obrigatoriamente na concessionária?',
            'first_revision' => 'Por que a primeira revisão aos 1.000 km é tão importante?',
            'chain_lubrication' => 'Como fazer a manutenção correta da corrente de transmissão?',
            'valve_adjustment' => 'Com que frequência preciso ajustar as válvulas?',
            'oil_type' => "Qual tipo de óleo devo usar na {make} {model}?"
        ],
        'pratico_moto' => [
            'maintenance_frequency' => "De quanto em quanto tempo devo revisar a {make} {model}?",
            'basic_maintenance' => 'Que manutenções posso fazer em casa?',
            'warning_signs' => 'Quais sinais indicam que preciso de manutenção urgente?',
            'seasonal_care' => 'Como preparar a moto para diferentes estações?'
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
            ['revisao' => '1ª Revisão', 'intervalo' => '1.000 km ou 6 meses', 'principais_servicos' => 'Primeira revisão, troca de óleo, ajustes', 'estimativa_custo' => $this->getCostRange($vehicleData, 1)],
            ['revisao' => '2ª Revisão', 'intervalo' => '5.000 km ou 12 meses', 'principais_servicos' => 'Óleo, freios, corrente, verificações gerais', 'estimativa_custo' => $this->getCostRange($vehicleData, 2)],
            ['revisao' => '3ª Revisão', 'intervalo' => '10.000 km ou 18 meses', 'principais_servicos' => 'Óleo, filtros, válvulas, embreagem', 'estimativa_custo' => $this->getCostRange($vehicleData, 3)],
            ['revisao' => '4ª Revisão', 'intervalo' => '15.000 km ou 24 meses', 'principais_servicos' => 'Óleo, velas, fluido de freio, rolamentos', 'estimativa_custo' => $this->getCostRange($vehicleData, 4)],
            ['revisao' => '5ª Revisão', 'intervalo' => '20.000 km ou 30 meses', 'principais_servicos' => 'Revisão ampla, velas, sincronização', 'estimativa_custo' => $this->getCostRange($vehicleData, 5)],
            ['revisao' => '6ª Revisão', 'intervalo' => '25.000 km ou 36 meses', 'principais_servicos' => 'Óleo, filtros, transmissão, suspensão', 'estimativa_custo' => $this->getCostRange($vehicleData, 6)]
        ];
    }

    public function generateDetailedSchedule(array $vehicleData): array
    {
        $style = $this->selectMaintenanceStyle($vehicleData);
        $schedule = [];

        // GARANTIR EXATAMENTE 6 REVISÕES - INTERVALOS ESPECÍFICOS PARA MOTO
        $revisionData = [
            1 => ['interval' => '1.000 km ou 6 meses', 'km' => '1.000'],
            2 => ['interval' => '5.000 km ou 12 meses', 'km' => '5.000'],
            3 => ['interval' => '10.000 km ou 18 meses', 'km' => '10.000'],
            4 => ['interval' => '15.000 km ou 24 meses', 'km' => '15.000'],
            5 => ['interval' => '20.000 km ou 30 meses', 'km' => '20.000'],
            6 => ['interval' => '25.000 km ou 36 meses', 'km' => '25.000']
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

    private function getServicesForRevision(int $revision, array $vehicleData): array
    {
        $intervalKeys = array_keys($this->maintenanceSchedule ?? []);

        if (isset($intervalKeys[$revision - 1])) {
            $key = $intervalKeys[$revision - 1];
            if (isset($this->maintenanceSchedule[$key])) {
                return $this->maintenanceSchedule[$key];
            }
        }

        return $this->getDefaultServicesForRevision($revision, $vehicleData);
    }

    private function getDefaultServicesForRevision(int $revision, array $vehicleData): array
    {
        switch ($revision) {
            case 1:
                return ['initial_revision', 'oil_change', 'chain_maintenance', 'valve_clearance'];
            case 2:
                return ['oil_change', 'brake_inspection', 'chain_maintenance', 'electrical_check'];
            case 3:
                return ['oil_change', 'air_filter', 'valve_clearance', 'clutch_check'];
            case 4:
                return ['oil_change', 'spark_plugs_check', 'brake_fluid_change', 'wheel_bearings'];
            case 5:
                return ['complete_revision', 'spark_plugs_change', 'timing_check', 'brake_system_full'];
            case 6:
                return ['oil_change', 'transmission_check', 'suspension_full', 'bearing_lubrification'];
            default:
                return ['oil_change', 'brake_inspection'];
        }
    }

    public function generatePreventiveMaintenance(array $vehicleData): array
    {
        $style = $this->selectMaintenanceStyle($vehicleData);

        $variations = [
            'simples' => [
                'verificacoes_mensais' => [
                    'Verificar óleo',
                    'Calibrar pneus',
                    'Testar luzes',
                    'Lubrificar corrente'
                ],
                'verificacoes_trimestrais' => [
                    'Fluido de freio',
                    'Corrente',
                    'Bateria',
                    'Desgaste dos pneus'
                ],
                'verificacoes_anuais' => [
                    'Regulagem da suspensão',
                    'Sistema de escapamento',
                    'Cabos e comandos',
                    'Lubrificação geral'
                ]
            ],
            'detalhado' => [
                'verificacoes_mensais' => [
                    'Monitoramento do nível e viscosidade do óleo lubrificante',
                    'Verificação da pressão dos pneumáticos conforme especificação',
                    'Inspeção completa do sistema de iluminação e sinalização',
                    'Verificação da tensão e lubrificação da corrente de transmissão'
                ],
                'verificacoes_trimestrais' => [
                    'Análise do nível e qualidade do fluido de freio',
                    'Manutenção completa da corrente incluindo limpeza e lubrificação',
                    'Verificação da bateria, terminais e sistema de carga',
                    'Inspeção detalhada do desgaste e alinhamento dos pneus'
                ],
                'verificacoes_anuais' => [
                    'Regulagem da suspensão conforme peso e uso',
                    'Inspeção completa do sistema de escapamento',
                    'Verificação do estado dos cabos e comandos',
                    'Lubrificação de articulações e pivôs',
                    'Verificação do alinhamento da direção'
                ]
            ]
        ];

        $selectedStyle = in_array($style, ['tecnico_moto', 'detalhado_moto', 'performance_moto']) ? 'detalhado' : 'simples';
        return $variations[$selectedStyle];
    }

    public function generateCriticalParts(array $vehicleData): array
    {
        return [
            [
                'componente' => 'Corrente de Transmissão',
                'intervalo_recomendado' => 'Verificação a cada 1.000 km',
                'observacao' => 'Uma corrente mal ajustada pode causar desgaste prematuro e até ruptura'
            ],
            [
                'componente' => 'Pastilhas de Freio',
                'intervalo_recomendado' => 'Verificação a cada 5.000 km',
                'observacao' => 'O desgaste das pastilhas compromete diretamente a segurança do piloto'
            ],
            [
                'componente' => 'Folgas das Válvulas',
                'intervalo_recomendado' => 'Ajuste a cada 10.000 km',
                'observacao' => 'Folgas incorretas afetam o desempenho e podem causar danos ao motor'
            ],
            [
                'componente' => 'Óleo da Suspensão',
                'intervalo_recomendado' => 'Troca a cada 20.000 km',
                'observacao' => 'Óleo degradado compromete a estabilidade e segurança na pilotagem'
            ],
            [
                'componente' => 'Velas de Ignição',
                'intervalo_recomendado' => 'Substituição a cada 15.000 km',
                'observacao' => 'Velas desgastadas causam falhas de ignição e perda de potência'
            ],
            [
                'componente' => 'Rolamentos das Rodas',
                'intervalo_recomendado' => 'Verificação a cada 15.000 km',
                'observacao' => 'Rolamentos desgastados comprometem a dirigibilidade e segurança'
            ]
        ];
    }

    public function generateTechnicalSpecs(array $vehicleData): array
    {
        return [
            'capacidade_oleo' => $this->getOilCapacity($vehicleData),
            'tipo_oleo_recomendado' => $this->getRecommendedOil($vehicleData),
            'intervalo_troca_oleo' => '5.000 km ou 6 meses',
            'filtro_oleo_original' => $this->getOriginalFilter($vehicleData),
            'capacidade_combustivel' => $this->getFuelCapacity($vehicleData),
            'fluido_freio' => 'DOT 4',
            'corrente_transmissao' => 'Verificar manual para especificação',
            'pressao_pneus' => $this->getTirePressure($vehicleData)
        ];
    }

    public function generateWarrantyInfo(array $vehicleData): array
    {
        return [
            'prazo_garantia' => '2 anos ou 40.000 km',
            'garantia_anticorrosao' => '3 anos',
            'garantia_itens_desgaste' => '6 meses ou 5.000 km',
            'observacoes_importantes' => 'Para manter a garantia válida, todas as revisões devem ser realizadas dentro do prazo estipulado. A primeira revisão aos 1.000 km é obrigatória.',
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

        // Agrupar motocicletas por faixas de ano e tipo
        $yearGroup = floor($year / 3) * 3;
        $segment = $this->getMotorcycleSegment($vehicleData);

        return "motorcycle_{$make}_{$segment}_{$yearGroup}";
    }

    private function getMotorcycleSegment(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');
        $model = strtolower($vehicleData['model'] ?? '');

        // Classificar por segmento motociclístico
        if (in_array($make, ['bmw', 'ducati', 'triumph', 'ktm']) || strpos($category, 'sport') !== false) {
            return 'premium';
        } elseif (strpos($category, 'scooter') !== false) {
            return 'scooter';
        } elseif (strpos($category, 'trail') !== false || strpos($category, 'adventure') !== false) {
            return 'adventure';
        } elseif (strpos($category, 'street') !== false || in_array($make, ['honda', 'yamaha'])) {
            return 'street';
        } elseif (strpos($category, 'custom') !== false || strpos($category, 'cruiser') !== false) {
            return 'custom';
        }

        return 'geral';
    }

    private function selectMaintenanceStyle(array $vehicleData): string
    {
        $segment = $this->getMotorcycleSegment($vehicleData);
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
            $availableStyles = ['tecnico_moto']; // Fallback seguro
        }

        // Preferência por segmento
        $preferredStyles = [
            'premium' => ['performance_moto', 'detalhado_moto'],
            'adventure' => ['detalhado_moto', 'tecnico_moto'],
            'scooter' => ['simples_moto', 'tecnico_moto'],
            'street' => ['tecnico_moto', 'detalhado_moto'],
            'custom' => ['detalhado_moto', 'performance_moto']
        ];

        $preferred = $preferredStyles[$segment] ?? ['tecnico_moto'];
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
        $styleData = $this->maintenanceStyles[$style] ?? $this->maintenanceStyles['tecnico_moto'];
        $services = [];

        foreach ($serviceKeys as $key) {
            if (isset($styleData[$key])) {
                $services[] = $styleData[$key];
            } else {
                $services[] = $this->getMotorcycleServiceFallback($key);
            }
        }

        return $services;
    }

    private function getMotorcycleServiceFallback(string $serviceKey): string
    {
        $fallbacks = [
            'initial_revision' => 'Primeira revisão com verificação de assentamento',
            'fluid_levels' => 'Verificação de níveis de fluidos',
            'cable_lubrication' => 'Lubrificação de cabos e articulações',
            'electrical_check' => 'Verificação do sistema elétrico',
            'spark_plugs_check' => 'Verificação das velas de ignição',
            'air_filter' => 'Substituição do filtro de ar',
            'clutch_check' => 'Inspeção do sistema de embreagem',
            'electrical_full' => 'Verificação completa do sistema elétrico',
            'injection_clean' => 'Limpeza do sistema de injeção',
            'fuel_system' => 'Verificação do sistema de alimentação',
            'wheel_bearings' => 'Verificação dos rolamentos das rodas',
            'steering_check' => 'Inspeção do sistema de direção',
            'brake_fluid_change' => 'Troca do fluido de freio',
            'cooling_system' => 'Verificação do sistema de arrefecimento',
            'complete_revision' => 'Revisão ampla com verificação de todos os sistemas',
            'spark_plugs_change' => 'Substituição das velas de ignição',
            'timing_check' => 'Verificação da sincronização do motor',
            'hoses_inspection' => 'Inspeção das mangueiras e tubulações',
            'brake_system_full' => 'Verificação completa do sistema de freios',
            'battery_check' => 'Verificação da bateria e sistema de carga',
            'transmission_check' => 'Verificação completa do sistema de transmissão',
            'exhaust_system' => 'Inspeção do sistema de escapamento',
            'suspension_full' => 'Inspeção completa da suspensão',
            'bearing_lubrication' => 'Lubrificação dos rolamentos e articulações',
            'cables_adjustment' => 'Ajuste de cabos e comandos'
        ];

        // CORREÇÃO: Fallback final para evitar erros
        return $fallbacks[$serviceKey] ?? 'Serviço especializado para motocicletas';
    }

    private function getCostRange(array $vehicleData, int $revisionNumber): string
    {
        $segment = $this->getMotorcycleSegment($vehicleData);
        $year = $vehicleData['year'] ?? date('Y');

        // Custos base por revisão para motocicletas
        $baseCosts = [
            1 => [180, 250],
            2 => [220, 280],
            3 => [320, 400],
            4 => [380, 450],
            5 => [450, 550],
            6 => [380, 480]
        ];

        $base = $baseCosts[$revisionNumber] ?? [300, 400];

        // Ajustar por segmento
        $multipliers = [
            'premium' => 1.8,
            'adventure' => 1.4,
            'custom' => 1.3,
            'street' => 1.0,
            'scooter' => 0.7,
            'geral' => 1.0
        ];

        $multiplier = $multipliers[$segment] ?? 1.0;

        // Motos mais antigas podem ter custos menores
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
                'A primeira revisão é obrigatória e essencial para verificar o assentamento dos componentes durante o período de amaciamento.',
                'Esta revisão inicial confirma o correto funcionamento de todos os sistemas após os primeiros quilômetros críticos.',
                'Fundamental para validar a garantia e detectar qualquer anomalia no funcionamento inicial da motocicleta.',
                'Revisão de amaciamento que verifica se todos os componentes estão funcionando conforme especificado.',
                'Primeira verificação completa após o período crítico de adaptação dos componentes.'
            ],
            3 => [
                'Revisão importante que inclui verificação das folgas das válvulas, crucial para o bom funcionamento do motor.',
                'Momento ideal para verificação de componentes que começam a mostrar sinais de desgaste normal.',
                'Esta revisão foca na manutenção de sistemas críticos para a performance da motocicleta.',
                'Verificação crucial para manter a confiabilidade da motocicleta a médio prazo.'
            ],
            5 => [
                'Revisão ampla que inclui sincronização do motor e verificações detalhadas de todos os sistemas.',
                'Momento importante para substituição de componentes críticos e verificação completa.',
                'Verificação completa que inclui componentes de maior durabilidade e importância.',
                'Revisão de renovação que inclui velas de ignição e ajustes de sincronização.',
                'Verificação abrangente que prepara a motocicleta para mais quilômetros de uso confiável.'
            ]
        ];

        $defaultObs = [
            'Revisão importante para manter o bom funcionamento e segurança da motocicleta.',
            'Verificação essencial para preservar a vida útil dos componentes.',
            'Manutenção preventiva para garantir segurança e performance.',
            'Inspeção programada para detectar e prevenir problemas futuros.'
        ];

        $availableObs = $observations[$revisionNumber] ?? $defaultObs;
        return $availableObs[array_rand($availableObs)];
    }

    private function getFAQStyle(string $vehicleKey): string
    {
        $usedStyles = self::$usedFAQStyles[$vehicleKey] ?? [];
        $availableStyles = array_diff(['basico_moto', 'completo_moto', 'pratico_moto'], $usedStyles);

        if (empty($availableStyles)) {
            self::$usedFAQStyles[$vehicleKey] = [];
            $availableStyles = ['basico_moto', 'completo_moto', 'pratico_moto'];
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
            'maintenance_frequency' => [
                'basico_moto' => 'Para motocicletas, geralmente recomenda-se revisões a cada 5.000 km ou 6 meses, com a primeira aos 1.000 km.',
                'completo_moto' => 'O intervalo padrão é de 5.000 km ou 6 meses (o que ocorrer primeiro), mas a primeira revisão obrigatória deve ser aos 1.000 km.',
                'pratico_moto' => 'A cada 5.000 km ou 6 meses, e não esqueça da primeira aos 1.000 km.'
            ],
            'dealership_required' => [
                'basico_moto' => 'Sim, desde que a oficina siga os procedimentos do manual e utilize peças originais.',
                'completo_moto' => 'A garantia não exige revisões na concessionária, mas é importante escolher oficinas especializadas em motocicletas.',
                'pratico_moto' => 'Pode fazer em oficina especializada, desde que use peças adequadas.'
            ],
            'first_revision' => [
                'basico_moto' => 'Sim, a revisão de 1.000 km é crítica pois verifica o assentamento inicial dos componentes.',
                'completo_moto' => 'A primeira revisão é obrigatória para verificar o assentamento dos componentes durante o amaciamento e manter a garantia.',
                'pratico_moto' => 'É obrigatória e super importante. Não pode pular essa!'
            ],
            'chain_lubrication' => [
                'basico_moto' => 'A corrente deve ser lubrificada a cada 500 km ou após exposição à chuva.',
                'completo_moto' => 'Lubrificação a cada 500 km ou após chuva, e verificação da tensão a cada 1.000 km.',
                'pratico_moto' => 'A cada 500 km ou depois da chuva. Tensão a cada 1.000 km.'
            ],
            'valve_adjustment' => 'As folgas das válvulas devem ser verificadas a cada 10.000 km conforme manual.',
            'oil_type' => "Use sempre o óleo especificado no manual da {$make} {$model}, geralmente 10W30 ou 10W40 semissintético.",
            'basic_maintenance' => 'Você pode verificar óleo, calibrar pneus, lubrificar corrente e testar luzes.',
            'warning_signs' => 'Ruídos estranhos, vibrações excessivas, dificuldade de partida ou mudanças no comportamento.',
            'seasonal_care' => 'No inverno, atenção à bateria e partida. No verão, monitore superaquecimento e pneus.'
        ];

        if (isset($answers[$questionKey])) {
            if (is_array($answers[$questionKey])) {
                return $answers[$questionKey][$style] ?? $answers[$questionKey][array_key_first($answers[$questionKey])];
            }
            return $answers[$questionKey];
        }

        return 'Consulte o manual do proprietário ou uma oficina especializada em motocicletas.';
    }

    private function getLifeTips(array $vehicleData): array
    {
        $segment = $this->getMotorcycleSegment($vehicleData);
        $category = strtolower($vehicleData['category'] ?? '');

        $baseTips = [
            'Evite acelerações bruscas durante o amaciamento',
            'Mantenha a corrente sempre lubrificada',
            'Verifique a calibragem dos pneus semanalmente',
            'Use combustível de qualidade',
            'Proteja a moto das intempéries',
            'Pilote defensivamente e respeite os limites'
        ];

        // Adicionar dicas específicas por segmento
        if ($segment === 'premium' || strpos($category, 'sport') !== false) {
            $baseTips[] = 'Respeite o tempo de aquecimento do motor';
            $baseTips[] = 'Use pneus adequados para o tipo de pilotagem';
        } elseif ($segment === 'adventure') {
            $baseTips[] = 'Verifique proteções após uso off-road';
            $baseTips[] = 'Limpe filtros após terrenos poeirentos';
        } elseif ($segment === 'scooter') {
            $baseTips[] = 'Verifique freios regularmente devido ao uso urbano';
            $baseTips[] = 'Mantenha o CVT limpo e ajustado';
        }

        return $baseTips;
    }

    private function getOilCapacity(array $vehicleData): string
    {
        $model = strtolower($vehicleData['model'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');

        // Capacidades baseadas no tipo/modelo
        if (strpos($category, 'scooter') !== false) {
            if (strpos($model, '125') !== false || strpos($model, '150') !== false) return '0.8 litros';
            return '1.0 litros';
        } elseif (strpos($model, 'cg') !== false || strpos($model, 'fan') !== false) {
            return '1.0 litros';
        } elseif (strpos($model, 'cb') !== false || strpos($model, 'fazer') !== false) {
            return '1.2 litros';
        } elseif (strpos($category, 'sport') !== false || strpos($model, '600') !== false) {
            return '3.5 litros';
        } elseif (strpos($category, 'adventure') !== false) {
            return '2.5 litros';
        }

        return '1.0 litros'; // Default para motos pequenas
    }

    private function getRecommendedOil(array $vehicleData): string
    {
        $recommendedOil = $vehicleData['recommended_oil'] ?? '';
        if (!empty($recommendedOil) && $recommendedOil !== 'NA') {
            return $recommendedOil;
        }

        $category = strtolower($vehicleData['category'] ?? '');
        $model = strtolower($vehicleData['model'] ?? '');
        $make = strtolower($vehicleData['make'] ?? '');

        // Baseado no tipo de moto
        if (strpos($category, 'sport') !== false || $make === 'ducati') {
            return 'Sintético 10W40 ou 10W50 para alto desempenho';
        } elseif (strpos($category, 'scooter') !== false) {
            return 'Semissintético 10W30 para scooters';
        } elseif (strpos($category, 'adventure') !== false) {
            return 'Sintético 10W40 para uso severo';
        }

        return 'Semissintético 10W30 ou 10W40, conforme especificação do fabricante';
    }

    private function getOriginalFilter(array $vehicleData): string
    {
        $make = strtolower($vehicleData['make'] ?? '');

        return match ($make) {
            'honda' => '15410-KFG-901',
            'yamaha' => '5GH-13440-50',
            'suzuki' => '16510-05240',
            'kawasaki' => '52010-0001',
            'bmw' => '11427721779',
            'ducati' => '44440031A',
            default => 'Consulte manual do proprietário'
        };
    }

    private function getFuelCapacity(array $vehicleData): string
    {
        $model = strtolower($vehicleData['model'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');

        // Capacidades baseadas no tipo
        if (strpos($category, 'scooter') !== false) return '5.3 litros';
        if (strpos($model, 'cg') !== false || strpos($model, 'fan') !== false) return '16 litros';
        if (strpos($model, 'cb') !== false || strpos($model, 'fazer') !== false) return '17.5 litros';
        if (strpos($category, 'sport') !== false) return '19 litros';
        if (strpos($category, 'adventure') !== false) return '22 litros';

        return '16 litros'; // Default
    }

    private function getTirePressure(array $vehicleData): string
    {
        $emptyFront = $vehicleData['pressure_empty_front'] ?? 29;
        $emptyRear = $vehicleData['pressure_empty_rear'] ?? 33;

        return "Dianteiro: {$emptyFront} PSI | Traseiro: {$emptyRear} PSI (sem carga)";
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
            $kmInt <= 1000 => 'R$ 180 - R$ 250',
            $kmInt <= 5000 => 'R$ 220 - R$ 280',
            $kmInt <= 10000 => 'R$ 320 - R$ 400',
            $kmInt <= 15000 => 'R$ 380 - R$ 450',
            $kmInt <= 20000 => 'R$ 450 - R$ 550',
            default => 'R$ 380 - R$ 480'
        };
    }

    private function getIntervalObservations(string $km, int $revisionNumber): string
    {
        return $this->getVariedObservation($revisionNumber, []);
    }
}
