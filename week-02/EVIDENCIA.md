# ASII-28 - Evidencia de semana 2

Fecha de validación: 15 de agosto de 2026.

Todos los comandos se ejecutaron desde la raíz del worktree local en la rama
`feature/asii-28-qa-funcional-didhyerortiz`. No se realizó `push` ni se creó un PR.

## Evidencia 1 - Entorno PHP

Comando:

```bash
php --version
```

Resultado observado:

```text
PHP 8.3.31 (cli)
```

Resultado: **PASS**.

## Evidencia 2 - Sintaxis del ejemplo

Comando:

```bash
php -l docs/asii-28/week-02/ejemplos/validar-sustitucion.php
```

Resultado observado:

```text
No syntax errors detected in docs/asii-28/week-02/ejemplos/validar-sustitucion.php
```

Resultado: **PASS**.

## Evidencia 3 - Sustitución LSP

Comando:

```bash
php docs/asii-28/week-02/ejemplos/validar-sustitucion.php
```

Resultado observado:

```text
PASS: CasoManual devuelve el contrato esperado
PASS: CasoReprueba sustituye al caso ejecutable
PASS: CasoManual conserva la versión
PASS: el resultado conserva la campaña
PASS: CasoReprueba conserva el tenant
PASS: la re-prueba conserva el defecto
PASS: la re-prueba conserva el caso original
PASS: el resultado conserva el rol
PASS: el resultado conserva el ejecutor
PASS: la ejecución conserva evidencia
PASS: el resultado proviene de la observación manual
PASS: un nuevo tipo se ejecuta sin cambiar el gestor
PASS: el borrador incompleto se identifica antes de ejecutar
PASS: el borrador no implementa el contrato ejecutable
PASS: el validador rechaza un borrador incompleto
PASS: una observación sin evidencia se rechaza
PASS: un contexto incompleto se rechaza
PASS: el gestor rechaza un borrador por su parámetro tipado
Resultado: 18/18 validaciones superadas.
```

Resultado: **PASS**.

## Evidencia 4 - Validación de Mermaid

Entorno observado:

```text
Node v24.19.0
npm 11.6.2
Mermaid CLI 11.16.0
```

La versión se instaló en un directorio temporal aislado y se ejecutó su binario
`mmdc.cmd` sobre las dos fuentes finales. Los comandos equivalentes documentados para
el proyecto usan la versión fijada:

```bash
npx --yes @mermaid-js/mermaid-cli@11.16.0 -i docs/asii-28/week-02/diagramas/01-diseno-antes-lsp.mmd -o docs/asii-28/week-02/diagramas/imagenes/diseno-antes-lsp.png -b white -w 2000 -s 2
npx --yes @mermaid-js/mermaid-cli@11.16.0 -i docs/asii-28/week-02/diagramas/02-diseno-despues-lsp.mmd -o docs/asii-28/week-02/diagramas/imagenes/diseno-despues-lsp.png -b white -w 1800 -s 2
```

Resultado observado:

```text
Generating single mermaid chart
Generating single mermaid chart
diseno-antes-lsp.png: 134986 bytes
diseno-despues-lsp.png: 253228 bytes
```

Ambas imágenes se abrieron y revisaron visualmente. Los nombres, relaciones, notas y
contratos corresponden con sus fuentes `.mmd`.

Resultado: **PASS**.

## Evidencia 5 - Integridad del cambio

Comandos:

```bash
git diff --check 7e22051..HEAD
git diff --name-status 7e22051..HEAD
```

Resultado observado:

- `git diff --check` no informó errores de espacios en blanco.
- El listado mostró únicamente 13 archivos dentro de
  `docs/asii-28/week-02/`.
- No se modificaron documentos de otros módulos ni artefactos de semana 1.

Resultado: **PASS**.

## Evidencia 6 - Historial local

Antes de agregar este archivo se verificaron 12 commits temáticos para semana 2:

```text
4471afd docs: declara uso de IA
a8a7c67 docs: completa guía semanal
d680066 docs: renderiza diagramas LSP
c6c964f docs: registra validación LSP
79132fd test: demuestra sustitución LSP
7432a8f docs: define contrato LSP
be6f3e6 docs: modela solución LSP
2daf3c0 docs: modela violación LSP
fb50089 docs: explica principio LSP
07a0375 docs: agrega criterios QA
04723f1 docs: define requisitos QA
0978437 docs: inicia semana 2
```

La cantidad supera el mínimo de 10 solicitado para el trabajo local.

Resultado: **PASS**.

## Limitaciones

- La entrega propone el diseño; no integra todavía estas clases con la aplicación.
- La demostración no persiste campañas, evidencias ni defectos.
- No se adjuntan capturas de una UI porque semana 2 no implementa una interfaz.
- El issue privado no pudo consultarse sin autenticación; la entrega se basó en
  `docs/weekly-plan.md`, el módulo asignado y la documentación existente de ASII-28.
- Dos procesos `npx` paralelos colisionaron en la caché de npm; la validación final se
  repitió correctamente con Mermaid CLI 11.16.0 instalado en un directorio temporal.
- La publicación remota queda pendiente de autorización final del estudiante.
