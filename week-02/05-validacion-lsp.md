# ASII-28 - Validación del diseño LSP

## 1. Objetivo

Comprobar que `CasoManual` y `CasoReprueba` pueden utilizarse mediante
`CasoRegresionEjecutable` sin modificar `GestorCampana`, y que un borrador incompleto
queda fuera del contrato.

La validación usa datos ficticios y un ejemplo PHP autocontenido. No ejecuta funciones
clínicas ni afirma que el diseño ya esté integrado en el HIS.

## 2. Escenarios de sustitución

| ID | Escenario | Acción | Resultado esperado | Criterio |
|---|---|---|---|---|
| VS-01 | Sustituir el contrato por `CasoManual` | Ejecutar con un contexto válido | Devuelve `ResultadoEjecucion`. | CA-06, CA-14 |
| VS-02 | Sustituir el contrato por `CasoReprueba` | Ejecutar en la misma lista | Devuelve el mismo tipo sin cambiar el gestor. | CA-14 |
| VS-03 | Mezclar dos implementaciones válidas | Recorrer la campaña | Se obtienen dos resultados controlados. | CA-06, CA-15 |
| VS-04 | Conservar el contexto | Ejecutar ambas implementaciones | Los resultados mantienen la versión recibida. | RNF-02, RNF-03 |
| VS-05 | Incorporar un borrador incompleto | Comprobar su tipo antes de construir la campaña | No implementa `CasoRegresionEjecutable`. | CA-02, CA-16 |
| VS-06 | Agregar otra implementación válida | Implementar el mismo contrato | El algoritmo del gestor no requiere modificaciones. | CA-15 |

## 3. Demostración ejecutable

Archivo:

```text
ejemplos/validar-sustitucion.php
```

Comandos ejecutados desde la raíz del repositorio:

```bash
php -l docs/asii-28/week-02/ejemplos/validar-sustitucion.php
php docs/asii-28/week-02/ejemplos/validar-sustitucion.php
```

Resultado observado el 14 de agosto de 2026:

```text
No syntax errors detected in docs/asii-28/week-02/ejemplos/validar-sustitucion.php
PASS: el gestor ejecuta ambas implementaciones
PASS: CasoManual devuelve el contrato esperado
PASS: CasoReprueba sustituye al caso ejecutable
PASS: CasoManual conserva la versión
PASS: CasoReprueba conserva la versión
PASS: el borrador incompleto se identifica antes de ejecutar
PASS: el borrador no implementa el contrato ejecutable
Resultado: 7/7 validaciones superadas.
```

## 4. Interpretación

La demostración cumple el contrato observable porque:

- el gestor recibe una lista tipada por la interfaz;
- no contiene `instanceof`, `switch` ni condiciones por tipo de caso;
- ambas implementaciones aceptan el mismo `ContextoEjecucion`;
- ambas devuelven `ResultadoEjecucion` y conservan la versión;
- el borrador no puede sustituir a un caso ejecutable;
- una falla de validación del borrador ocurre antes de ejecutar la campaña.

## 5. Limitaciones

- El ejemplo no persiste campañas, evidencias ni defectos.
- Los resultados se construyen con valores controlados para probar el contrato.
- La interacción humana de una prueba manual se representa de forma conceptual.
- La integración con el módulo funcional y sus adaptadores corresponde a fases
  posteriores del proyecto.

Estas limitaciones no invalidan la prueba de diseño: la propiedad evaluada es la
sustitución entre implementaciones del mismo contrato, no el funcionamiento completo
del sistema de QA.
