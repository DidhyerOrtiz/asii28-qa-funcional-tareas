<?php

declare(strict_types=1);

interface CasoRegresionEjecutable
{
    public function obtenerId(): string;

    public function obtenerRequisitoId(): string;

    public function ejecutar(
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion;
}

final class ContextoEjecucion
{
    public readonly string $version;
    public readonly string $ambiente;
    public readonly string $tenant;
    public readonly string $rol;
    public readonly string $ejecutor;
    public readonly string $fecha;

    public function __construct(
        string $version,
        string $ambiente,
        string $tenant,
        string $rol,
        string $ejecutor,
        string $fecha,
    ) {
        $this->version = textoObligatorio($version, 'versión');
        $this->ambiente = textoObligatorio($ambiente, 'ambiente');
        $this->tenant = textoObligatorio($tenant, 'tenant');
        $this->rol = textoObligatorio($rol, 'rol');
        $this->ejecutor = textoObligatorio($ejecutor, 'ejecutor');
        $this->fecha = textoObligatorio($fecha, 'fecha');
    }
}

final class ObservacionManual
{
    /** @var list<string> */
    public readonly array $evidencias;
    public readonly string $estado;
    public readonly string $resultadoObservado;
    public readonly ?string $causaBloqueo;

    /** @param list<string> $evidencias */
    public function __construct(
        string $estado,
        string $resultadoObservado,
        array $evidencias,
        ?string $causaBloqueo = null,
    ) {
        if (!in_array($estado, ['Aprobado', 'Fallido', 'Bloqueado'], true)) {
            throw new InvalidArgumentException('Estado de ejecución inválido.');
        }

        if ($evidencias === []) {
            throw new InvalidArgumentException('Toda ejecución requiere evidencia.');
        }

        if ($estado === 'Bloqueado' && trim((string) $causaBloqueo) === '') {
            throw new InvalidArgumentException('Un bloqueo requiere una causa.');
        }

        $this->estado = $estado;
        $this->resultadoObservado = textoObligatorio(
            $resultadoObservado,
            'resultado observado',
        );
        $this->evidencias = $evidencias;
        $this->causaBloqueo = $causaBloqueo;
    }
}

final readonly class ResultadoEjecucion
{
    /** @param list<string> $evidencias */
    public function __construct(
        public string $casoId,
        public string $requisitoId,
        public string $estado,
        public string $version,
        public string $ambiente,
        public string $tenant,
        public string $rol,
        public string $ejecutor,
        public string $fecha,
        public string $resultadoObservado,
        public array $evidencias,
        public ?string $causaBloqueo = null,
    ) {
    }
}

final class CasoManual implements CasoRegresionEjecutable
{
    /** @var list<string> */
    private array $pasos;
    private string $id;
    private string $requisitoId;
    private string $resultadoEsperado;

    /** @param list<string> $pasos */
    public function __construct(
        string $id,
        string $requisitoId,
        array $pasos,
        string $resultadoEsperado,
    ) {
        if ($pasos === [] || array_filter($pasos, 'esTextoVacio') !== []) {
            throw new InvalidArgumentException('Los pasos deben estar completos.');
        }

        $this->id = textoObligatorio($id, 'identificador');
        $this->requisitoId = textoObligatorio($requisitoId, 'requisito');
        $this->pasos = $pasos;
        $this->resultadoEsperado = textoObligatorio(
            $resultadoEsperado,
            'resultado esperado',
        );
    }

    public function obtenerId(): string
    {
        return $this->id;
    }

    public function obtenerRequisitoId(): string
    {
        return $this->requisitoId;
    }

    public function ejecutar(
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion {
        return crearResultado($this, $contexto, $observacion);
    }
}

final class CasoReprueba implements CasoRegresionEjecutable
{
    private string $id;
    private string $requisitoId;
    private string $defectoOrigen;

    public function __construct(
        string $id,
        string $requisitoId,
        string $defectoOrigen,
        private readonly CasoRegresionEjecutable $casoOriginal,
    ) {
        $this->id = textoObligatorio($id, 'identificador');
        $this->requisitoId = textoObligatorio($requisitoId, 'requisito');
        $this->defectoOrigen = textoObligatorio($defectoOrigen, 'defecto de origen');
    }

    public function obtenerId(): string
    {
        return $this->id;
    }

    public function obtenerRequisitoId(): string
    {
        return $this->requisitoId;
    }

    public function ejecutar(
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion {
        return crearResultado($this, $contexto, $observacion);
    }
}

final class CasoNegativoManual implements CasoRegresionEjecutable
{
    private string $id;
    private string $requisitoId;

    public function __construct(string $id, string $requisitoId)
    {
        $this->id = textoObligatorio($id, 'identificador');
        $this->requisitoId = textoObligatorio($requisitoId, 'requisito');
    }

    public function obtenerId(): string
    {
        return $this->id;
    }

    public function obtenerRequisitoId(): string
    {
        return $this->requisitoId;
    }

    public function ejecutar(
        ContextoEjecucion $contexto,
        ObservacionManual $observacion,
    ): ResultadoEjecucion {
        return crearResultado($this, $contexto, $observacion);
    }
}

final readonly class CasoBorrador
{
    /** @param list<string> $pasos */
    public function __construct(
        public string $requisitoId,
        public array $pasos,
        public string $resultadoEsperado,
    ) {
    }

    public function estaCompleto(): bool
    {
        return trim($this->requisitoId) !== ''
            && $this->pasos !== []
            && array_filter($this->pasos, 'esTextoVacio') === []
            && trim($this->resultadoEsperado) !== '';
    }
}

final class ValidadorCaso
{
    public function convertir(string $id, CasoBorrador $borrador): CasoManual
    {
        if (!$borrador->estaCompleto()) {
            throw new InvalidArgumentException('El borrador está incompleto.');
        }

        return new CasoManual(
            $id,
            $borrador->requisitoId,
            $borrador->pasos,
            $borrador->resultadoEsperado,
        );
    }
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

function textoObligatorio(string $valor, string $campo): string
{
    $valor = trim($valor);

    if ($valor === '') {
        throw new InvalidArgumentException("El campo {$campo} es obligatorio.");
    }

    return $valor;
}

function esTextoVacio(string $valor): bool
{
    return trim($valor) === '';
}

function crearResultado(
    CasoRegresionEjecutable $caso,
    ContextoEjecucion $contexto,
    ObservacionManual $observacion,
): ResultadoEjecucion {
    return new ResultadoEjecucion(
        $caso->obtenerId(),
        $caso->obtenerRequisitoId(),
        $observacion->estado,
        $contexto->version,
        $contexto->ambiente,
        $contexto->tenant,
        $contexto->rol,
        $contexto->ejecutor,
        $contexto->fecha,
        $observacion->resultadoObservado,
        $observacion->evidencias,
        $observacion->causaBloqueo,
    );
}

function verificar(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        fwrite(STDERR, "FAIL: {$mensaje}\n");
        exit(1);
    }

    echo "PASS: {$mensaje}\n";
}

$contexto = new ContextoEjecucion(
    'commit-demo-2026',
    'qa-local',
    'TENANT-DEMO',
    'ANALISTA_QA',
    'QA-DEMO-01',
    '2026-08-15T00:00:00Z',
);

$borradorValido = new CasoBorrador(
    'RF-04',
    ['Preparar datos ficticios', 'Ejecutar flujo', 'Comparar resultado'],
    'El flujo responde según el criterio de aceptación',
);
$validador = new ValidadorCaso();
$casoManual = $validador->convertir('QA-001', $borradorValido);
$casoReprueba = new CasoReprueba('QA-002', 'RF-07', 'DEF-DEMO-01', $casoManual);
$casoNegativo = new CasoNegativoManual('QA-003', 'RF-05');

$observacionAprobada = new ObservacionManual(
    'Aprobado',
    'El comportamiento coincide con el resultado esperado.',
    ['evidencia://captura-aprobada'],
);
$observacionFallida = new ObservacionManual(
    'Fallido',
    'El sistema permitió una operación que debía rechazar.',
    ['evidencia://captura-fallo'],
);

$gestor = new GestorCampana();
$resultadoManual = $gestor->ejecutarCaso($casoManual, $contexto, $observacionAprobada);
$resultadoReprueba = $gestor->ejecutarCaso($casoReprueba, $contexto, $observacionAprobada);
$resultadoNegativo = $gestor->ejecutarCaso($casoNegativo, $contexto, $observacionFallida);

verificar($resultadoManual instanceof ResultadoEjecucion, 'CasoManual devuelve el contrato esperado');
verificar($resultadoReprueba instanceof ResultadoEjecucion, 'CasoReprueba sustituye al caso ejecutable');
verificar($resultadoManual->version === $contexto->version, 'CasoManual conserva la versión');
verificar($resultadoReprueba->tenant === $contexto->tenant, 'CasoReprueba conserva el tenant');
verificar($resultadoManual->rol === $contexto->rol, 'el resultado conserva el rol');
verificar($resultadoManual->ejecutor === $contexto->ejecutor, 'el resultado conserva el ejecutor');
verificar($resultadoManual->evidencias !== [], 'la ejecución conserva evidencia');
verificar(
    $resultadoManual->resultadoObservado === $observacionAprobada->resultadoObservado,
    'el resultado proviene de la observación manual',
);
verificar($resultadoNegativo->estado === 'Fallido', 'un nuevo tipo se ejecuta sin cambiar el gestor');

$borradorIncompleto = new CasoBorrador('RF-04', [], '');
verificar(!$borradorIncompleto->estaCompleto(), 'el borrador incompleto se identifica antes de ejecutar');
verificar(
    !($borradorIncompleto instanceof CasoRegresionEjecutable),
    'el borrador no implementa el contrato ejecutable',
);

$rechazadoPorValidador = false;

try {
    $validador->convertir('QA-004', $borradorIncompleto);
} catch (InvalidArgumentException) {
    $rechazadoPorValidador = true;
}

verificar($rechazadoPorValidador, 'el validador rechaza un borrador incompleto');

$rechazadoSinEvidencia = false;

try {
    new ObservacionManual('Aprobado', 'Resultado observado', []);
} catch (InvalidArgumentException) {
    $rechazadoSinEvidencia = true;
}

verificar($rechazadoSinEvidencia, 'una observación sin evidencia se rechaza');

$rechazadoSinContexto = false;

try {
    new ContextoEjecucion('commit-demo-2026', '', 'TENANT-DEMO', 'ANALISTA_QA', 'QA-DEMO-01', '2026-08-15');
} catch (InvalidArgumentException) {
    $rechazadoSinContexto = true;
}

verificar($rechazadoSinContexto, 'un contexto incompleto se rechaza');

$rechazadoPorTipo = false;

try {
    $gestor->ejecutarCaso($borradorIncompleto, $contexto, $observacionAprobada);
} catch (TypeError) {
    $rechazadoPorTipo = true;
}

verificar($rechazadoPorTipo, 'el gestor rechaza un borrador por su parámetro tipado');

echo "Resultado: 15/15 validaciones superadas.\n";
