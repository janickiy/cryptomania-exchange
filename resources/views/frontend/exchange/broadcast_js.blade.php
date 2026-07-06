<script>
    let activeBroadcastStockPairId = null;
    let isStockPairSummaryBroadcastBound = false;

    function broadcast() {
        const echo = window.Echo;

        if (!echo || typeof echo.channel !== 'function') {
            return;
        }

        let channelPrefix = '{{ channel_prefix() }}';
        let stockPairId = defaultStockPairId;

        if (activeBroadcastStockPairId && activeBroadcastStockPairId !== stockPairId && typeof echo.leave === 'function') {
            echo.leave(channelPrefix + 'orders.' + activeBroadcastStockPairId);
            echo.leave(channelPrefix + 'exchange.' + activeBroadcastStockPairId);

            if (user) {
                echo.leave(channelPrefix + 'orders.' + activeBroadcastStockPairId + '.' + user.id);
                echo.leave(channelPrefix + 'exchange.' + activeBroadcastStockPairId + '.' + user.id);
            }
        }

        if (activeBroadcastStockPairId === stockPairId) {
            return;
        }

        activeBroadcastStockPairId = stockPairId;

        echo.channel(channelPrefix + 'orders.' + stockPairId).listen('Exchange.BroadcastOrder', (data) => {
            processOrderTable(data);
        }).listen('Exchange.BroadcastCancelOrder', (data) => {
            processOrderTable(data);
        });

        if (user && typeof echo.private === 'function') {
            echo.private(channelPrefix + 'orders.' + stockPairId + '.' + user.id).listen('Exchange.BroadcastPrivateOrder', (data) => {
                updateMyOpenOrderTable(data);
                updateOrderFormOnOrderPlace(data);
            }).listen('Exchange.BroadcastPrivateCancelOrder', (data) => {
                updateMyOpenOrderTable(data);
                updateOrderFormOnCancel(data);
            });
        }


        echo.channel(channelPrefix + 'exchange.' + stockPairId).listen('Exchange.BroadcastStockExchange', (data) => {

            $.each(data.exchangedOrders[exchangeTypeBuy], function (_, buy) {
                if (buy) {
                    updateOrdersBook(buyOrderTable, buy);
                    updateOrderBookTotal($('#total_buy_order_in_base'), buy.total);
                    if (buy.is_maker) {
                        buy.amount = bcmul(buy.amount, '-1');
                        buy.total = bcmul(buy.total, '-1');
                        updateTradeHistory(tradeHistoryTable, buy);
                    }
                }
            });

            $.each(data.exchangedOrders[exchangeTypeSell], function (_, sell) {
                if (sell) {
                    updateOrdersBook(sellOrderTable, sell);
                    updateOrderBookTotal($('#total_sell_order_in_item'), sell.amount);
                    if (sell.is_maker) {
                        sell.amount = bcmul(sell.amount, '-1');
                        sell.total = bcmul(sell.total, '-1');
                        updateTradeHistory(tradeHistoryTable, sell);
                    }
                }

            });

            updateChart(data.chartData);
            updateStockPairSummary(data.stockPairSummary)
        }).listen('Exchange.BroadcastSettlementOrders', (data) => {
            let table = sellOrderTable;
            let removeAmount = data.amount;

            if (data.exchange_type == exchangeTypeBuy) {
                table = buyOrderTable;
                removeAmount = data.total;
            }
            updateOrdersBook(table, data);
            updateOrderBookTotal($('#total_sell_order_in_item'), removeAmount);
        });

        if (!isStockPairSummaryBroadcastBound) {
            echo.channel(channelPrefix + 'exchange').listen('Exchange.BroadcastStockPairSummary', (data) => {
                updateStockMarketTable(data);
            });
            isStockPairSummaryBroadcastBound = true;
        }

        if (user && typeof echo.private === 'function') {
            echo.private(channelPrefix + 'exchange.' + stockPairId + '.' + user.id).listen('Exchange.BroadcastPrivateStockExchange', (data) => {
                $.each(data, function (_, order) {
                    if (order) {
                        updateMyOpenOrderTable(order);
                        updateMyTradeHisty(order);
                        updateOrderFormOnExchange(order);
                    }
                });
            }).listen('Exchange.BroadcastPrivateSettlementOrder', (data) => {
                $.each(data, function (_, order) {
                    if (order) {
                        updateMyOpenOrderTable(order);
                    }
                });
            });


        }
    }
</script>
