# Evidencia Git - avances de semanas 1 y 2

## Repositorio personal

- Propietario: `DidhyerOrtiz`
- Repositorio: [`asii28-qa-funcional-tareas`](https://github.com/DidhyerOrtiz/asii28-qa-funcional-tareas)
- Visibilidad: publica
- Rama estable: [`main`](https://github.com/DidhyerOrtiz/asii28-qa-funcional-tareas/tree/main)
- Rama de integracion: [`developer`](https://github.com/DidhyerOrtiz/asii28-qa-funcional-tareas/tree/developer)

## Puntos de revision

| Entrega | Rama | Etiqueta | Contenido principal |
|---|---|---|---|
| Semana 1 | [`feature/week-01-uml`](https://github.com/DidhyerOrtiz/asii28-qa-funcional-tareas/tree/feature/week-01-uml) | `semana-1-entrega` | Alcance, actores, casos de uso y tres diagramas UML |
| Semana 2 | [`feature/week-02-solid`](https://github.com/DidhyerOrtiz/asii28-qa-funcional-tareas/tree/feature/week-02-solid) | `semana-2-entrega` | RF/RNF, criterios, LSP, diagramas, PHP y evidencia |

El historial de ambas semanas fue exportado conservando la autoria, fechas y
commits academicos originales. El repositorio personal excluye el codigo del HIS
y los artefactos de semanas posteriores; contiene unicamente la evidencia que se
solicita compartir publicamente.

## Trazabilidad con el proyecto colaborativo

| Elemento | Referencia | Estado al preparar esta evidencia |
|---|---|---|
| Seguimiento personal | [Issue #28](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026/issues/28) | Semanas documentadas mediante comentarios |
| Integracion semana 1 | [PR #45](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026/pull/45) | Fusionado |
| Integracion semana 2 | [PR #54](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026/pull/54) | Pendiente de revision |

Los pull requests pertenecen al repositorio colaborativo. Este repositorio
personal funciona como evidencia publica e independiente para la entrega en
Canvas.

## Comandos de verificacion

```bash
git clone https://github.com/DidhyerOrtiz/asii28-qa-funcional-tareas.git
cd asii28-qa-funcional-tareas
git branch --all
git tag --list
git log --oneline --decorate --graph --all
php -l week-02/ejemplos/validar-sustitucion.php
php week-02/ejemplos/validar-sustitucion.php
```

Resultado funcional esperado:

```text
Resultado: 20/20 validaciones superadas.
```
