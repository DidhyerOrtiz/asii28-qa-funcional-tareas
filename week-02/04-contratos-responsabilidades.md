# ASII-28 - Contratos, responsabilidades y dependencias

## 1. Contrato de sustitución

### 1.1 Operación común

```text
CasoRegresionEjecutable.ejecutar(
    ContextoEjecucion contexto,
    ObservacionManual observacion
): ResultadoEjecucion
```

El contrato se aplica por igual a `CasoManual`, `CasoReprueba` y cualquier futura
variante ejecutable. Una implementación no puede cambiar estas condiciones para
obtener un trato especial dentro de `GestorCampana`.

### 1.2 Precondiciones

- El caso fue validado y tiene identificador, requisito, pasos y resultado esperado.
- `ContextoEjecucion` identifica campaña, versión, ambiente, tenant, rol, ejecutor,
  datos ficticios utilizados y fecha.
- `ObservacionManual` contiene estado, resultado observado y evidencia.
- Los datos requeridos por el caso son ficticios y están disponibles.
- El rol indicado tiene autorización para el escenario evaluado.
- La implementación no exige precondiciones adicionales al momento de sustituir a
  otro caso ejecutable.

`CasoReprueba` necesita un defecto de origen, pero esa regla se comprueba al construir
el objeto. Su método `ejecutar()` sigue aceptando el mismo contexto que el resto de
implementaciones.

### 1.3 Postcondiciones

- La operación devuelve un `ResultadoEjecucion` no nulo.
- El resultado conserva campaña, caso, requisito, versión, ambiente, tenant, rol,
  ejecutor, datos ficticios utilizados y fecha.
- El estado pertenece a `Aprobado`, `Fallido` o `Bloqueado` cuando hubo intento de
  ejecución; `No ejecutado` se asigna por planificación, sin invocar `ejecutar()`.
- Un resultado `Fallido` contiene resultado observado y referencias de evidencia.
- Un resultado `Bloqueado` contiene una causa verificable.
- El resultado no se inventa: se construye desde la observación suministrada por el
  Analista QA.
- La operación no lanza una excepción de "operación no soportada".

### 1.4 Invariantes

- El identificador y el requisito vinculado al caso no cambian durante la ejecución.
- El tenant y el rol del contexto no se sustituyen silenciosamente.
- La evidencia queda asociada con la ejecución que la produjo.
- No se incorpora información clínica identificable.
- Un fallo solo genera un defecto confirmado cuando puede reproducirse.
- `GestorCampana` no depende de clases concretas de casos.

### 1.5 Límite de autorización

- Transportar tenant y rol no equivale a autorizar una operación.
- La API deberá rechazar tenant ausente o distinto al autorizado antes de consultar
  información QA.
- La autorización RBAC deberá comprobar el permiso de la operación antes de delegar en
  los servicios del módulo.
- Estos controles son criterios del diseño; el ejemplo LSP no sustituye las futuras
  pruebas de seguridad de la integración Laravel.

## 2. Responsabilidades

| Elemento | Responsabilidad | No debe hacer |
|---|---|---|
| `CasoRegresionEjecutable` | Definir la operación común de los casos que pueden participar en una campaña. | Conocer almacenamiento, UI o tipos concretos. |
| `CasoManual` | Representar pasos ya validados y convertir la observación manual en un resultado uniforme. | Inventar un resultado o aceptar datos incompletos como ejecutables. |
| `CasoReprueba` | Representar la repetición de un caso relacionado con un defecto y conservar esa trazabilidad. | Fortalecer las precondiciones de `ejecutar()`. |
| `CasoNegativoManual` | Demostrar que una nueva variante válida puede usar el mismo contrato. | Exigir cambios o condiciones especiales en el gestor. |
| `CasoBorrador` | Conservar información en preparación y permitir comprobar si está completa. | Implementar el contrato ejecutable antes de ser validado. |
| `ValidadorCaso` | Validar el borrador y convertirlo en un caso manual completo. | Ejecutar campañas o registrar evidencias. |
| `ContextoEjecucion` | Transportar campaña, versión, ambiente, tenant, rol, ejecutor, datos ficticios y fecha. | Decidir el resultado del caso o autorizar por sí mismo. |
| `ObservacionManual` | Transportar el estado, resultado observado, evidencia y causa de bloqueo registrados por QA. | Inventar datos del contexto. |
| `ResultadoEjecucion` | Consolidar el caso, contexto y observación de forma trazable. | Ejecutar pasos o crear defectos por sí mismo. |
| `GestorCampana` | Procesar un caso a través del contrato tipado y devolver su resultado. | Preguntar si un objeto es `CasoManual`, `CasoReprueba` o `CasoBorrador`. |
| `GestorEvidencia` | Conservar evidencia y devolver una referencia identificable. | Cambiar el estado obtenido durante la ejecución. |
| `GestorDefectos` | Procesar fallos reproducibles y mantener su relación con el módulo responsable. | Confirmar defectos sin evidencia reproducible. |

El diagrama mejorado se concentra en la sustitución de casos y omite los colaboradores
secundarios `GestorEvidencia` y `GestorDefectos`; sus responsabilidades se conservan en
esta tabla para el diseño posterior del módulo.

## 3. Dirección de dependencias

```text
GestorCampana --> CasoRegresionEjecutable
CasoManual ----> CasoRegresionEjecutable
CasoReprueba --> CasoRegresionEjecutable
CasoNegativoManual --> CasoRegresionEjecutable
GestorCampana --> ContextoEjecucion
GestorCampana --> ObservacionManual
CasoManual ----> ResultadoEjecucion
CasoReprueba --> ResultadoEjecucion
GestorCampana -.-> GestorEvidencia  [colaborador propuesto]
GestorCampana -.-> GestorDefectos  [colaborador propuesto]
ValidadorCaso --> CasoBorrador
ValidadorCaso --> CasoManual
```

La dependencia importante para LSP es `GestorCampana ->
CasoRegresionEjecutable`. Su método `ejecutarCaso()` exige ese tipo en tiempo de
ejecución y conoce la promesa observable, no los detalles de cada variante.
`CasoBorrador` queda en otro flujo y solo puede entrar a una campaña después de que
`ValidadorCaso` produzca un objeto válido.

## 4. Decisiones y límites

- El diseño utiliza una interfaz porque el comportamiento común es más importante que
  compartir estado mediante herencia.
- Las validaciones estructurales ocurren antes de la ejecución para evitar objetos que
  prometan operaciones imposibles.
- Los errores de ambiente se representan con un resultado `Bloqueado`; los errores de
  programación no se ocultan como resultados funcionales.
- El pseudocódigo documenta la intención arquitectónica. No afirma que estas clases ya
  estén implementadas en la aplicación Laravel del repositorio.
