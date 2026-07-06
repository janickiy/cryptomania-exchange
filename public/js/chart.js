(function (window) {
    'use strict';

    var COLORS = {
        up: '#65dcc7',
        down: '#e98995',
        text: '#dffdf6',
        tooltipText: '#eefaf6',
        ma12: '#65dcc7',
        ma40: '#8f98ff',
        ma200: '#f1b65e',
        grid: 'rgba(101, 220, 199, 0.45)'
    };
    var MOVING_AVERAGES = [
        {name: 'MA12', days: 12, color: COLORS.ma12},
        {name: 'MA40', days: 40, color: COLORS.ma40},
        {name: 'MA200', days: 200, color: COLORS.ma200}
    ];
    var ZOOM_PRESETS = [360, 1440, 2880, 5760, 10080, 20160, 43200];

    window.eChart = window.eChart || null;

    function readGlobal(getter, fallback) {
        try {
            var value = getter();

            return value === null || typeof value === 'undefined' ? fallback : value;
        } catch (error) {
            return fallback;
        }
    }

    function getGlobalChartData() {
        return readGlobal(function () {
            return chartData;
        }, []);
    }

    function getGlobalZoom() {
        return parseInt(readGlobal(function () {
            return defaultZoom;
        }, 0), 10) || 0;
    }

    function getGlobalInterval() {
        return parseInt(readGlobal(function () {
            return defaultInterval;
        }, 1), 10) || 1;
    }

    function normalizeRawData(rawData) {
        return Array.isArray(rawData) ? rawData : [];
    }

    function splitData(rawData) {
        var normalizedData = normalizeRawData(rawData);
        var categoryData = [];
        var values = [];

        normalizedData.forEach(function (item) {
            categoryData.push({
                value: item[0],
                textStyle: {
                    fontSize: 10,
                    color: COLORS.text
                }
            });
            values.push([
                item[1],
                item[2],
                item[3],
                item[4]
            ]);
        });

        return {
            categoryData: categoryData,
            values: values,
            volumes: []
        };
    }

    function calculateMACD(data) {
        var result = [];
        var firstPeriod = 12;
        var secondPeriod = 26;

        data.values.forEach(function (value, index) {
            var firstLength = index < firstPeriod ? index + 1 : firstPeriod;
            var secondLength = index < secondPeriod ? index + 1 : secondPeriod;
            var firstSum = 0;
            var secondSum = 0;

            for (var offset = 0; offset < secondLength; offset += 1) {
                secondSum += parseFloat(data.values[index - offset][1]);

                if (offset < firstLength) {
                    firstSum += parseFloat(data.values[index - offset][1]);
                }
            }

            var macd = (firstSum / firstLength) - (secondSum / secondLength);
            result.push([index, Number(macd.toFixed(3)), macd <= 0 ? -1 : 1]);
        });

        return result;
    }

    function calculateMA(dayCount, data) {
        var result = [];

        data.values.forEach(function (value, index) {
            var sum = 0;

            if (index < dayCount) {
                result.push('0');
                return;
            }

            for (var offset = 0; offset < dayCount; offset += 1) {
                sum += parseFloat(data.values[index - offset][1]);
            }

            result.push(parseFloat((sum / dayCount).toFixed(3)) || 0);
        });

        return result;
    }

    function calculateZoom(data, zoomValue, intervalValue) {
        var sourceData = normalizeRawData(data || getGlobalChartData());
        var zoom = typeof zoomValue === 'undefined' ? getGlobalZoom() : parseInt(zoomValue, 10);
        var interval = typeof intervalValue === 'undefined' ? getGlobalInterval() : parseInt(intervalValue, 10);
        var visibleCandlesticks = sourceData.length;

        if (ZOOM_PRESETS.indexOf(zoom) >= 0) {
            visibleCandlesticks = parseInt(zoom / interval, 10);
        }

        if (visibleCandlesticks < 1) {
            visibleCandlesticks = 1;
        }

        if (sourceData.length <= visibleCandlesticks) {
            return 0;
        }

        return (100 - ((visibleCandlesticks - 1) * 100 / sourceData.length)).toFixed(8);
    }

    function getChartInstance(element) {
        if (!element || !window.echarts) {
            return null;
        }

        return window.echarts.getInstanceByDom(element) || window.echarts.init(element);
    }

    function baseAxisLine(opacity) {
        return {
            lineStyle: {
                color: '#08f',
                opacity: opacity || 0.55,
                type: 'dotted'
            }
        };
    }

    function splitLine(color, opacity) {
        return {
            show: true,
            lineStyle: {
                color: color || COLORS.grid,
                opacity: opacity,
                type: 'dotted'
            }
        };
    }

    function movingAverageSeries(echartData) {
        return MOVING_AVERAGES.map(function (average) {
            return {
                name: average.name,
                type: 'line',
                data: calculateMA(average.days, echartData),
                smooth: true,
                showSymbol: false,
                lineStyle: {
                    normal: {
                        opacity: 0.5,
                        width: 1,
                        color: average.color
                    }
                }
            };
        });
    }

    function buildChartOption(echartData, zoom) {
        return {
            textStyle: {
                color: COLORS.text,
                fontSize: 10
            },
            color: [COLORS.ma12, COLORS.ma40, COLORS.ma200],
            backgroundColor: 'transparent',
            animation: false,
            legend: {
                data: MOVING_AVERAGES.map(function (average) {
                    return average.name;
                }),
                textStyle: {
                    color: COLORS.text,
                    fontSize: 10
                }
            },
            tooltip: {
                trigger: 'axis',
                triggerOn: 'click',
                axisPointer: {
                    type: 'cross'
                },
                backgroundColor: 'rgba(9, 18, 16, 0.92)',
                borderWidth: 1,
                borderColor: 'rgba(222, 255, 248, 0.36)',
                padding: 10,
                textStyle: {
                    color: COLORS.tooltipText,
                    fontSize: 10
                },
                position: function (pos, params, el, elRect, size) {
                    var position = {top: '10.05%'};
                    position[['left', 'right'][Number(pos[0] < size.viewSize[0] / 2)]] = '30';

                    return position;
                }
            },
            axisPointer: {
                link: {xAxisIndex: 'all'},
                label: {
                    backgroundColor: 'rgba(9, 18, 16, 0.9)',
                    color: COLORS.tooltipText
                }
            },
            toolbox: {
                show: false
            },
            visualMap: {
                show: false,
                seriesIndex: 4,
                dimension: 2,
                pieces: [
                    {value: 1, color: COLORS.up},
                    {value: -1, color: COLORS.down}
                ]
            },
            grid: [
                {
                    left: '7%',
                    right: '0',
                    height: '66%',
                    top: '7%'
                },
                {
                    left: '7%',
                    right: '0',
                    top: '78%',
                    height: '13%'
                }
            ],
            xAxis: [
                {
                    type: 'category',
                    data: echartData.categoryData,
                    scale: true,
                    boundaryGap: true,
                    axisLine: baseAxisLine(),
                    splitLine: splitLine(),
                    splitNumber: 10,
                    axisPointer: {
                        z: 100
                    }
                },
                {
                    type: 'category',
                    gridIndex: 1,
                    data: echartData.categoryData,
                    scale: true,
                    boundaryGap: true,
                    axisLine: {
                        show: false
                    },
                    axisTick: {show: false},
                    splitLine: splitLine('rgba(143, 244, 221, 0.52)', 0.4),
                    axisLabel: {show: false},
                    splitNumber: 1
                }
            ],
            yAxis: [
                {
                    axisLine: baseAxisLine(),
                    scale: true,
                    splitLine: splitLine(),
                    splitNumber: 5,
                    axisLabel: {
                        fontSize: 10,
                        color: COLORS.text
                    }
                },
                {
                    type: 'value',
                    scale: true,
                    gridIndex: 1,
                    splitNumber: 1,
                    splitLine: splitLine(COLORS.grid, 0.4),
                    axisLine: {
                        lineStyle: {
                            color: COLORS.grid,
                            opacity: 0.4,
                            type: 'dotted'
                        }
                    },
                    axisTick: {show: false},
                    splitArea: {
                        show: true,
                        areaStyle: {
                            color: 'rgba(101, 220, 199, 0.16)',
                            opacity: 0.4
                        }
                    },
                    axisLabel: {show: false}
                }
            ],
            dataZoom: [
                {
                    xAxisIndex: [0, 1],
                    start: zoom,
                    end: 100
                },
                {
                    show: true,
                    xAxisIndex: [0, 1],
                    type: 'slider',
                    top: '92%',
                    start: zoom,
                    end: 100,
                    borderColor: 'rgba(222, 255, 248, 0.22)',
                    fillerColor: 'rgba(101, 220, 199, 0.18)',
                    dataBackground: {
                        lineStyle: {
                            color: 'rgba(101, 220, 199, 0.35)'
                        },
                        areaStyle: {
                            color: 'rgba(101, 220, 199, 0.1)'
                        }
                    },
                    selectedDataBackground: {
                        lineStyle: {
                            color: 'rgba(241, 182, 94, 0.45)'
                        },
                        areaStyle: {
                            color: 'rgba(241, 182, 94, 0.14)'
                        }
                    },
                    handleStyle: {
                        color: COLORS.up,
                        borderColor: COLORS.text
                    },
                    textStyle: {
                        color: COLORS.text
                    }
                }
            ],
            series: [
                {
                    name: 'Chart Data',
                    type: 'candlestick',
                    data: echartData.values,
                    itemStyle: {
                        normal: {
                            color: COLORS.up,
                            color0: COLORS.down,
                            borderColor: COLORS.up,
                            borderColor0: COLORS.down
                        }
                    },
                    tooltip: {
                        formatter: function (param) {
                            var item = param[0];

                            return [
                                'Date: ' + item.name + '<hr size=1 style="margin: 3px 0">',
                                'Open: ' + item.data[0] + '<br/>',
                                'Close: ' + item.data[1] + '<br/>',
                                'Lowest: ' + item.data[2] + '<br/>',
                                'Highest: ' + item.data[3] + '<br/>'
                            ].join('');
                        }
                    }
                }
            ].concat(movingAverageSeries(echartData), [
                {
                    name: 'Change',
                    type: 'bar',
                    barWidth: '55%',
                    xAxisIndex: 1,
                    yAxisIndex: 1,
                    data: calculateMACD(echartData)
                }
            ])
        };
    }

    function makeChart(element, data) {
        var sourceData = normalizeRawData(data);
        var chart = getChartInstance(element);
        var echartData = splitData(sourceData);

        if (!chart) {
            return null;
        }

        window.eChart = chart;
        chart.setOption(buildChartOption(echartData, calculateZoom(sourceData)), true);

        return chart;
    }

    window.splitData = splitData;
    window.calculateMACD = calculateMACD;
    window.calculateMA = calculateMA;
    window.calculateZoom = calculateZoom;
    window.makeChart = makeChart;
})(window);
