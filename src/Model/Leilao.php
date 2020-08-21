<?php

namespace Alura\Leilao\Model;

class Leilao {
	/** @var Lance[] */
	private $lances;
	/** @var string */
	private $descricao;
	private $finalizado;

	public function __construct(string $descricao) {
		$this->descricao = $descricao;
		$this->lances = [];
		$this->finalizado = false;
	}

	private function ehDoUltimoUsuario(Lance $lance): bool{
		$ultimoLance = $this->lances[array_key_last($this->lances)];
		return $lance->getUsuario() == $ultimoLance->getUsuario();
	}

	public function recebeLance(Lance $lance) {
		if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
			throw new \DomainException('Não é possível fazer dois lances seguidos do mesmo usuário');
		}

		$totalLancesUsuario = $this->quantidadeLancesPorUsuario($lance->getUsuario());
		if ($totalLancesUsuario >= 5) {
			throw new \DomainException('Não é possível fazer mais que 5 lances por usuário');

		}

		$this->lances[] = $lance;
	}

	/**
	 * @return Lance[]
	 */
	public function getLances(): array
	{
		return $this->lances;
	}

	public function finaliza() {
		$this->finalizado = true;
	}

	public function estaFinalizado(): bool {
		return $this->finalizado;
	}

	private function quantidadeLancesPorUsuario(Usuario $usuario): int{
		$totalLancesUsuario = array_reduce(
			$this->lances,
			function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
				if ($lanceAtual->getUsuario() == $usuario) {
					return $totalAcumulado + 1;
				}
				return $totalAcumulado;
			},
			0
		);

		return $totalLancesUsuario;
	}
}
