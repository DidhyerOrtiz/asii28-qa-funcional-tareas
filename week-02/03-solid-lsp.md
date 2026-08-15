# ASII-28 - Aplicación del principio LSP

## 1. Principio seleccionado

La fuente obligatoria describe el principio de sustitución de Liskov de la siguiente
forma:

> "Una clase derivada de otra debe poder ser sustituida por su clase base y debemos
> garantizar que los métodos de la primera no provoquen un mal funcionamiento de los
> métodos de la clase base".

Fuente: Rodríguez González, L. M. (s. f.). *Principios básicos del diseño de
software*. MVP Cluster. https://mvpcluster.com/diseno-de-software-2/

Para ASII-28, esto significa que cualquier objeto recibido como caso ejecutable debe
aceptar el mismo contexto y observación manual, devolver el mismo tipo de resultado y
conservar las reglas de trazabilidad. En la formulación canónica, un objeto del subtipo
debe poder utilizarse donde el consumidor espera el tipo base sin alterar la corrección
del programa. `GestorCampana` no debe conocer la clase concreta para decidir si puede
procesar un caso.

## 2. Motivo de la elección

Una campaña puede contener casos manuales ordinarios, re-pruebas de defectos y futuras
variantes válidas. Estas clases representan comportamientos distintos, pero durante
la ejecución prometen lo mismo: recibir un contexto válido y la observación registrada
por el Analista QA, y producir un resultado controlado. LSP permite expresar esa
promesa y verificar que todas las variantes sean sustituibles.

El caso problemático es `CasoBorrador`. Un borrador puede carecer de requisito, pasos
o resultado esperado, por lo que todavía no puede cumplir la operación `ejecutar()`.
Hacerlo heredar de un tipo ejecutable crea una promesa falsa.

## 3. Diseño anterior: violación

El diseño inicial propone una clase base `CasoRegresion` con `ejecutar()`. De ella
heredan `CasoManual`, `CasoReprueba` y `CasoBorrador`.

```php
abstract class CasoRegresion
{
    abstract public function ejecutar(
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion;
}

final class CasoBorrador extends CasoRegresion
{
    public function ejecutar(
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion {
        throw new LogicException('El borrador no se puede ejecutar');
    }
}
```

Aunque el tipo base promete una ejecución, `CasoBorrador` lanza una excepción por una
operación que no soporta. Si el gestor recibe cualquier `CasoRegresion`, debe preguntar
por su tipo concreto o arriesgarse a interrumpir la campaña.

```php
if ($caso instanceof CasoBorrador) {
    throw new LogicException('El gestor no puede procesar este tipo');
}

$resultado = $caso->ejecutar($contexto, $observacion);
```

### Efectos de la violación

- `CasoBorrador` no cumple la postcondición prometida por el tipo base.
- El consumidor necesita una excepción por clase concreta.
- Agregar variantes aumenta las condiciones dentro de `GestorCampana`.
- Una sustitución puede detener la campaña con una excepción inesperada.
- El tipo base deja de comunicar con precisión qué objetos son ejecutables.

## 4. Diseño mejorado

La solución define `CasoRegresionEjecutable` únicamente para casos validados. Tanto
`CasoManual` como `CasoReprueba` implementan ese contrato, reciben
`ContextoEjecucion` y `ObservacionManual`, y devuelven `ResultadoEjecucion`.

`CasoBorrador` deja de ser un subtipo ejecutable. `ValidadorCaso` comprueba que tenga
la información mínima y, cuando cumple las reglas, lo convierte en un `CasoManual`.
La invalidez se resuelve antes de construir el objeto ejecutable, no alterando el
comportamiento de `ejecutar()`.

```php
interface CasoRegresionEjecutable
{
    public function ejecutar(
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion;
}

final class GestorCampana
{
    public function ejecutarCaso(
        CasoRegresionEjecutable $caso,
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion {
        return $caso->ejecutar($contexto, $observacion);
    }
}
```

## 5. Resultado del cambio

| Aspecto | Antes | Después |
|---|---|---|
| Tipo recibido por el gestor | Cualquier `CasoRegresion` | Solo `CasoRegresionEjecutable` |
| Borradores | Heredan una operación que no soportan | Permanecen fuera del contrato ejecutable |
| Decisión por tipo | El gestor usa `instanceof` | No existe comprobación de clase concreta |
| Resultado | Puede aparecer una excepción no prometida | Todo caso válido devuelve `ResultadoEjecucion` |
| Extensión | Cada variante puede exigir cambios | Una variante válida implementa el contrato |

El cambio cumple LSP porque una implementación válida puede sustituir a otra sin
fortalecer las precondiciones de `ejecutar()`, debilitar sus postcondiciones ni romper
los invariantes de trazabilidad.
