# Theme EADUEMS

Projeto documental inicial do novo tema `eaduems` para Moodle 5.x, inspirado visualmente no `theme_eaduems_portal`, mas conduzido com foco mais rigoroso em boas praticas do ecossistema Moodle.

## Objetivo

Construir um novo tema para o LMS Moodle 5.x que:

1. preserve os acertos visuais e de experiencia do portal atual
2. reduza acoplamentos excessivos com rotas e fluxos especificos
3. organize melhor arquitetura, roteamento, templates e CSS
4. favoreca manutencao, extensibilidade e compatibilidade futura com o core Moodle

## Decisoes iniciais consolidadas

1. nome oficial do novo tema: `eaduems`
2. base de heranca: `boost`

### Justificativa da base `boost`

O novo tema nascera sobre `boost` porque isso favorece:

1. menor acoplamento estrutural com temas intermediarios
2. maior controle sobre templates, layout e roteamento
3. melhor clareza entre codigo do core e codigo do tema
4. arquitetura mais adequada a um projeto que pretende evitar herancas excessivas, arquivos monoliticos e logica espalhada

O `boost_union` permanece como referencia funcional e visual secundaria, nao como base estrutural do novo projeto.

## Estado atual

Neste momento, este diretorio funciona como espaco de documentacao e planejamento. O desenvolvimento do codigo do novo tema sera iniciado em uma fase posterior, apos consolidacao do escopo, arquitetura e criterios de implementacao.

## Documentos iniciais

- [Plano de acao](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_PLAN.md)
- [Lista de tarefas](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_TODO.md)
- [Analise inicial da home atual](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_FRONTPAGE_REVIEW_2026-06-18.md)
- [Wireframe e arquitetura da nova home](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_HOMEPAGE_WIREFRAME_2026-06-18.md)
- [Mapa de componentes da home](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_HOMEPAGE_COMPONENT_MAP_2026-06-18.md)
- [Estrutura tecnica do tema sobre boost](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_TECHNICAL_STRUCTURE_2026-06-18.md)
- [Fundacao de dark mode](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_DARK_MODE_FOUNDATION_2026-06-18.md)
- [Mapa de templates e regioes](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_TEMPLATE_REGION_MAP_2026-06-18.md)
- [Estrategia de settings](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_SETTINGS_STRATEGY_2026-06-18.md)
- [Primeiro recorte do MVP](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_MVP_SCOPE_2026-06-18.md)
- [Backlog inicial de implementacao](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_IMPLEMENTATION_BACKLOG_2026-06-18.md)
- [Estrategia inicial de scaffolding](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_SCAFFOLDING_STRATEGY_2026-06-18.md)
- [Mapa visual de reaproveitamento](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_VISUAL_REUSE_MAP_2026-06-18.md)
- [Mapa visual de redesenho](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_VISUAL_REDESIGN_MAP_2026-06-18.md)
- [Decisao sobre inicio do scaffold real](/D:/wamp64/www/moodle_teste/public/theme/eaduems/docs/THEME_EADUEMS_SCAFFOLD_START_DECISION_2026-06-18.md)

## Premissas arquiteturais iniciais

1. usar o tema atual como referencia visual e conceitual, nao como base de copia estrutural
2. priorizar classes/helpers menores e responsabilidades mais bem separadas
3. evitar arquivos monoliticos para CSS, logica de roteamento e montagem de contexto
4. manter escopo de interface institucional focado em experiencia do usuario final
5. reduzir overrides de core ao minimo necessario e documenta-los explicitamente
6. preparar o tema desde o inicio para suportar dark mode
7. manter o aspecto visual o mais próximo possível do `eaduems_portal`, sem reproduzir sua arquitetura acoplada
8. prever painel administrativo por seção relevante da home
9. adotar paleta semântica de configuração, evitando nomes explícitos de cor

## Proximo marco

Definir:

1. primeiro passo do scaffold real
2. ordem de criacao dos arquivos iniciais do tema
3. limite exato do primeiro commit estrutural

