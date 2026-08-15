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
| VS-01 | Sustituir el contrato por `CasoManual` | Procesar contexto y observación válidos | Devuelve `ResultadoEjecucion`. | CA-06, CA-14 |
| VS-02 | Sustituir el contrato por `CasoReprueba` | Procesarlo con el mismo método | Devuelve el mismo tipo sin cambiar el gestor. | CA-14 |
| VS-03 | Conservar el contexto | Procesar ambas implementaciones | Los resultados mantienen versión, tenant, rol y ejecutor. | CA-17 |
| VS-04 | Conservar la observación | Registrar resultado y evidencia manual | El resultado reproduce lo observado, sin inventar una aprobación. | CA-06, CA-19 |
| VS-05 | Incorporar un borrador incompleto | Pasarlo al método tipado | PHP lo rechaza porque no implementa el contrato. | CA-02, CA-16 |
| VS-06 | Agregar `CasoNegativoManual` | Implementar y procesar el mismo contrato | El gestor obtiene el estado `Fallido` sin modificaciones. | CA-15 |

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

Resultado observado el 15 de agosto de 2026:

```text
No syntax errors detected in docs/asii-28/week-02/ejemplos/validar-sustitucion.php
PASS: CasoManual devuelve el contrato esperado
PASS: CasoReprueba sustituye al caso ejecutable
PASS: CasoManual conserva la versión
PASS: CasoReprueba conserva el tenant
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
Resultado: 15/15 validaciones superadas.
```

## 4. Interpretación

La demostración cumple el contrato observable porque:

- el gestor recibe cada caso mediante un parámetro tipado por la interfaz;
- no contiene `instanceof`, `switch` ni condiciones por tipo de caso;
- las implementaciones aceptan el mismo contexto y observación manual;
- los resultados conservan contexto, resultado observado y evidencia;
- `CasoNegativoManual` extiende el diseño sin modificar el gestor;
- el método tipado rechaza un borrador en tiempo de ejecución;
- `ValidadorCaso` convierte únicamente borradores completos;
- el contexto incompleto y las observaciones sin evidencia se rechazan.

## 5. Limitaciones

- El ejemplo no persiste campañas, evidencias ni defectos.
- Las observaciones se construyen con valores controlados para probar el contrato.
- La interacción humana de una prueba manual se representa de forma conceptual.
- La integración con el módulo funcional y sus adaptadores corresponde a fases
  posteriores del proyecto.

Estas limitaciones no invalidan la prueba de diseño: la propiedad evaluada es la
sustitución entre implementaciones del mismo contrato, no el funcionamiento completo
del sistema de QA.
