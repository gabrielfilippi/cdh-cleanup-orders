# CodeHive Cleanup Old Orders

## Descrição

O plugin **CodeHive Cleanup Old Orders** foi desenvolvido para remover pedidos antigos no WooCommerce com base em determinadas condições de tempo e status.

## Recursos

- Remoção automatizada de pedidos antigos baseada em status e data de criação.
- Integração com a funcionalidade de tabelas personalizadas do WooCommerce (HPOS).
- Configuração de limites por lote para processar pedidos.
- Gerenciamento de tarefas agendadas com **WP Cron**.

## Instalação

1. Faça o download do repositório ou instale diretamente pelo WordPress.
2. Ative o plugin no painel do WordPress.

## Requisitos

- WordPress 5.6 ou superior.
- WooCommerce 4.0 ou superior.
- PHP 7.4 ou superior.

## Licença

Este projeto é distribuído sob a licença [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.txt).

## Funcionalidades do Código

### Configuração do Limite de Processamento com CDH_CLEANUP_ORDERS_BATCH_LIMIT

O plugin permite configurar o limite de processamento de pedidos por lote utilizando a constante CDH_CLEANUP_ORDERS_BATCH_LIMIT.

Por padrão, o limite é definido como 200 pedidos por lote.

Para ajustar o limite, adicione a seguinte linha ao arquivo wp-config.php:

```php
define('CDH_CLEANUP_ORDERS_BATCH_LIMIT', 500);

Essa configuração é útil para otimizar o desempenho em servidores com diferentes capacidades de processamento.