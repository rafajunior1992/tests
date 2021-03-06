<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase {

	private $leiloeiro;

	protected function setUp(): void{
		$this->leiloeiro = new Avaliador();
	}

	/**
	 *@dataProvider leilaoEmOrdemCrescente
	 *@dataProvider leilaoEmOrdemDecrescente
	 *@dataProvider leilaoEmOrdemAleatoria
	 */

	public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Leilao $leilao) {
		// Act - When
		$this->leiloeiro->avalia($leilao);

		$maiorValor = $this->leiloeiro->getMaiorValor();

		// Assert - Then
		self::assertEquals(3000, $maiorValor);
	}

	/**
	 *@dataProvider leilaoEmOrdemCrescente
	 *@dataProvider leilaoEmOrdemDecrescente
	 *@dataProvider leilaoEmOrdemAleatoria
	 */

	public function testAvaliadorDeveEncontrarOMenorValorDeLances(Leilao $leilao) {
		// Act - When
		$this->leiloeiro->avalia($leilao);

		$menorValor = $this->leiloeiro->getMenorValor();

		// Assert - Then
		self::assertEquals(2000, $menorValor);
	}

	/**
	 *@dataProvider leilaoEmOrdemCrescente
	 *@dataProvider leilaoEmOrdemDecrescente
	 *@dataProvider leilaoEmOrdemAleatoria
	 */

	public function testAvaliadorDeveEncontrar3MaioresValores(Leilao $leilao) {
		$this->leiloeiro->avalia($leilao);

		$maiores = $this->leiloeiro->getMaioresLances();

		static::assertCount(3, $maiores);
		static::assertEquals(3000, $maiores[0]->getValor());
		static::assertEquals(2500, $maiores[1]->getValor());
		static::assertEquals(2000, $maiores[2]->getValor());
	}

	public function testLeilaoVazioNaoPodeSerAvaliado() {
		$this->expectException(\DomainException::class);
		$this->expectExceptionMessage('Não é possível avaliar leilão vazio');
		$leilao = new Leilao('Fusca Azul');
		$this->leiloeiro->avalia($leilao);
	}

	public function testLeilaoFinalizadoNaoPodeSerAvaliado() {
		$this->expectException(\DomainException::class);
		$this->expectExceptionMessage('Leilão já finalizado');
		$leilao = new Leilao('Fusca 1985');
		$leilao->recebeLance(new Lance(new Usuario('Teste'), 2000));
		$leilao->finaliza();

		$this->leiloeiro->avalia($leilao);
	}

	// DADOS *********************************
	public function leilaoEmOrdemCrescente() {
		// Arrange - Given
		$leilao = new Leilao('Fiat 147 0KM');

		$maria = new Usuario('Maria');
		$joao = new Usuario('João');
		$ana = new Usuario('Ana');

		$leilao->recebeLance(new Lance($joao, 2000));
		$leilao->recebeLance(new Lance($maria, 2500));
		$leilao->recebeLance(new Lance($ana, 3000));

		return [
			'Ordem Crescente' => [$leilao],
		];

	}

	public function leilaoEmOrdemDecrescente() {
		// Arrange - Given
		$leilao = new Leilao('Fiat 147 0KM');

		$maria = new Usuario('Maria');
		$joao = new Usuario('João');
		$ana = new Usuario('Ana');

		$leilao->recebeLance(new Lance($ana, 2000));
		$leilao->recebeLance(new Lance($maria, 2500));
		$leilao->recebeLance(new Lance($joao, 3000));

		return [
			'Ordem Decrescente' => [$leilao],
		];
	}

	public function leilaoEmOrdemAleatoria() {
		// Arrange - Given
		$leilao = new Leilao('Fiat 147 0KM');

		$maria = new Usuario('Maria');
		$joao = new Usuario('João');
		$ana = new Usuario('Ana');

		$leilao->recebeLance(new Lance($maria, 2500));
		$leilao->recebeLance(new Lance($joao, 2000));
		$leilao->recebeLance(new Lance($ana, 3000));

		return [
			'Ordem Aleatória' => [$leilao],
		];
	}
}
