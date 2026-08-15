<?php

declare(strict_types=1);

interface CasoRegresionEjecutable
{
    public function obtenerId(): string;

    public function obtenerRequisitoId(): string;

    public function ejecutar(ContextoEjecucion $contexto): ResultadoEjecucion;
}

final readonly class ContextoEjecucion
{
    public function __construct(
        public string $version,
        public string $ambiente,
        public string $tenant,
        public string $rol,
    ) {
    }
}

final readonly class ResultadoEjecucion
{
    /** @param list<string> $evidencias */
    public function __construct(
        public string $casoId,
        public string $estado,
        public string $version,
        public array $evidencias,
        public ?string $causaBloqueo = null,
    ) {
    }
}

final readonly class CasoManual implements CasoRegresionEjecutable
{
    /** @param list<string> $pasos */
    public function __construct(
        private string $id,
        private string $requisitoId,
        private array $pasos,
        private string $resultadoEsperado,
    ) {
    }

    public function obtenerId(): string
    {
        return $this->id;
    }

    public function obtenerRequisitoId(): string
    {
        return $this->requisitoId;
    }

    public function ejecutar(ContextoEjecucion $contexto): ResultadoEjecucion
    {
        return new ResultadoEjecucion(
            $this->id,
            'Aprobado',
            $contexto->version,
            ['evidencia://' . $this->id],
        );
    }
}

final readonly class CasoReprueba implements CasoRegresionEjecutable
{
    public function __construct(
        private string $id,
        private string $requisitoId,
        private string $defectoOrigen,
        private CasoRegresionEjecutable $casoOriginal,
    ) {
    }

    public function obtenerId(): string
    {
        return $this->id;
    }

    public function obtenerRequisitoId(): string
    {
        return $this->requisitoId;
    }

    public function ejecutar(ContextoEjecucion $contexto): ResultadoEjecucion
    {
        return new ResultadoEjecucion(
            $this->id,
            'Aprobado',
            $contexto->version,
            ['evidencia://' . $this->id, 'defecto://' . $this->defectoOrigen],
        );
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
        return $this->requisitoId !== ''
            && $this->pasos !== []
            && $this->resultadoEsperado !== '';
    }
}

final class GestorCampana
{
    /**
     * @param list<CasoRegresionEjecutable> $casos
     * @return list<ResultadoEjecucion>
     */
    public function ejecutar(
        array $casos,
        ContextoEjecucion $contexto,
    ): array {
        $resultados = [];

        foreach ($casos as $caso) {
            $resultados[] = $caso->ejecutar($contexto);
        }

        return $resultados;
    }
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
);

$casoManual = new CasoManual(
    'QA-001',
    'RF-04',
    ['Preparar datos ficticios', 'Ejecutar flujo', 'Comparar resultado'],
    'El flujo responde según el criterio de aceptación',
);

$casoReprueba = new CasoReprueba(
    'QA-002',
    'RF-07',
    'DEF-DEMO-01',
    $casoManual,
);

$gestor = new GestorCampana();
$resultados = $gestor->ejecutar([$casoManual, $casoReprueba], $contexto);

verificar(count($resultados) === 2, 'el gestor ejecuta ambas implementaciones');
verificar($resultados[0] instanceof ResultadoEjecucion, 'CasoManual devuelve el contrato esperado');
verificar($resultados[1] instanceof ResultadoEjecucion, 'CasoReprueba sustituye al caso ejecutable');
verificar($resultados[0]->version === $contexto->version, 'CasoManual conserva la versión');
verificar($resultados[1]->version === $contexto->version, 'CasoReprueba conserva la versión');

$borrador = new CasoBorrador('RF-04', [], '');
verificar(!$borrador->estaCompleto(), 'el borrador incompleto se identifica antes de ejecutar');
verificar(!($borrador instanceof CasoRegresionEjecutable), 'el borrador no implementa el contrato ejecutable');

echo "Resultado: 7/7 validaciones superadas.\n";
