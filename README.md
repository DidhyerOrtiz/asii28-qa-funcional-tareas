# ASII-28 - QA funcional y matriz de regresion

Repositorio personal publico de **Didhyer Alexander Ortiz Guevara**
([`DidhyerOrtiz`](https://github.com/DidhyerOrtiz)) para documentar los avances de
las semanas 1, 2, 4 y 7 a 11 de Analisis de Sistemas II.

**Modulo asignado:** ASII-28 - QA funcional, pruebas manuales y matriz de
regresion.

## Entregas

| Semana | Alcance | Evidencia | Rama de referencia |
|---|---|---|---|
| 1 | Diagnostico, actores, limites, procesos y diagramas UML | [`week-01/`](week-01/) | [`feature/week-01-uml`](../../tree/feature/week-01-uml) |
| 2 | RF/RNF, criterios de aceptacion y aplicacion de LSP | [`week-02/`](week-02/) | [`feature/week-02-solid`](../../tree/feature/week-02-solid) |
| 4 | Arquitectura Repository, implementacion QA, regresion e informe final | [`week-04/`](week-04/) | [`feature/week-04-repository`](../../tree/feature/week-04-repository) |
| 7 | Componentes backend/frontend y refactor conceptual | [`week-07/`](week-07/) | `feature/week-07-11-design-ux` |
| 8 | Flujo UX, wireframes y reglas de interaccion | [`week-08/`](week-08/) | `feature/week-07-11-design-ux` |
| 9 | Usabilidad, accesibilidad y mejoras priorizadas | [`week-09/`](week-09/) | `feature/week-07-11-design-ux` |
| 10 | Diseno responsive y escenarios moviles | [`week-10/`](week-10/) | `feature/week-07-11-design-ux` |
| 11 | Prototipo navegable desktop/mobile | [`week-11/`](week-11/) | `feature/week-07-11-design-ux` |

## Modelo de ramas

El repositorio expone la estructura solicitada para separar trabajo estable,
integracion y avances por caracteristica:

```text
main
└── developer
    ├── feature/week-01-uml
    ├── feature/week-02-solid
    ├── feature/week-04-repository
    ├── feature/week-07-11-design-ux
    └── feature/repository-evidence
```

```mermaid
flowchart TB
    MAIN[main: entregas estables]
    DEV[developer: integracion]
    W1[feature/week-01-uml]
    W2[feature/week-02-solid]
    W4[feature/week-04-repository]
    W711[feature/week-07-11-design-ux]
    EVI[feature/repository-evidence]

    MAIN --> DEV
    DEV --> W1
    DEV --> W2
    DEV --> W4
    DEV --> W711
    DEV --> EVI
```

Las ramas de semana conservan puntos verificables del historial exportado desde
el repositorio colaborativo. Las etiquetas de entrega son:

- `semana-1-entrega`
- `semana-2-entrega`
- `semana-4-entrega`

La trazabilidad de ramas, commits y contribuciones al proyecto se encuentra en
[`EVIDENCIA_GIT.md`](EVIDENCIA_GIT.md).

## Semana 1

La primera semana define el contexto del modulo, sus actores, limites y casos de
uso. Incluye diagramas de casos de uso, actividad y secuencia, junto con sus
fuentes Mermaid editables.

- [Indice de semana 1](week-01/README.md)
- [Narrativa y alcance](week-01/01-narrativa-alcance.md)
- [Actores](week-01/02-actores.md)
- [Limites y procesos](week-01/03-limites-procesos.md)
- [Casos de uso](week-01/04-casos-de-uso.md)
- [Diagramas y fuentes editables](week-01/diagramas/)

## Semana 2

La segunda semana convierte el alcance en requisitos verificables, criterios de
aceptacion y trazabilidad. El ejemplo autocontenido demuestra la correccion de
una violacion del principio de sustitucion de Liskov (LSP).

- [Indice de semana 2](week-02/README.md)
- [Requisitos funcionales y no funcionales](week-02/01-requisitos-rf-rnf.md)
- [Criterios de aceptacion](week-02/02-criterios-aceptacion.md)
- [Aplicacion de LSP](week-02/03-solid-lsp.md)
- [Contratos y responsabilidades](week-02/04-contratos-responsabilidades.md)
- [Evidencia de validacion](week-02/EVIDENCIA.md)
- [Declaracion de uso de IA](week-02/DECLARACION_IA.md)

## Semana 4

La cuarta semana entrega el vertical Repository del Modulo 29 con arquitectura por
capas, contrato API, persistencia PostgreSQL, matriz de regresion, evidencia
reproducible y seis diagramas finales.

- [Indice de semana 4](week-04/README.md)
- [Informe final en PDF](week-04/informe-tarea-repository.pdf)
- [Informe editable en DOCX](week-04/informe-tarea-repository.docx)
- [Matriz de regresion](week-04/MATRIZ_REGRESION.md)
- [Evidencia reproducible](week-04/EVIDENCIA.md)
- [PR colaborativo #63](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026/pull/63)

## Semanas 7 a 11

Este bloque desarrolla componentes, experiencia por rol, evaluacion de
accesibilidad, comportamiento responsive y un prototipo navegable con datos
ficticios.

- [Semana 7: componentes y refactor](week-07/README.md)
- [Semana 8: flujo UX y wireframes](week-08/README.md)
- [Semana 9: usabilidad y accesibilidad](week-09/README.md)
- [Semana 10: responsive y movilidad](week-10/README.md)
- [Semana 11: prototipo navegable](week-11/README.md)
- [Evidencia consolidada](EVIDENCIA_SEMANAS_07_11.md)
- [Declaracion de uso de IA](DECLARACION_IA_SEMANAS_07_11.md)

## Validacion rapida

Desde la raiz del repositorio:

```bash
php -l week-02/ejemplos/validar-sustitucion.php
php week-02/ejemplos/validar-sustitucion.php
git branch --all
git log --oneline --decorate --graph --all
```

La demostracion PHP debe finalizar con `Resultado: 20/20 validaciones superadas.`
Todos los datos y escenarios son ficticios y no contienen informacion clinica
identificable.

## Proyecto colaborativo

Los avances se integran tambien en
[`compilations-teams/sistema-hospitalario-integrado-SistenasII-2026`](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026)
mediante el issue [`ASII-28`](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026/issues/28)
y pull requests sujetos a revision del equipo.
