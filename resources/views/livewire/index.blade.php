<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1" class="animate__animated animate__fadeIn animate__faster">
            Inventário de Produtos
        </flux:heading>
        <flux:text class="mb-6 mt-2 text-base animate__animated animate__fadeIn animate__fast">
            Consulte, analise e gerencie inventários pendentes de forma prática e segura.
        </flux:text>
    </div>
    <flux:separator variant="subtle"/>
    <div class="grid grid-cols-12 gap-4">
        <flux:card class="col-span-6 max-lg:col-span-12 animate__animated animate__fadeIn">
            <flux:heading size="xl" class="mb-4">Inventários Fechados</flux:heading>
            <div id="chartdiv"></div>
        </flux:card>
        <flux:card class="col-span-6 max-lg:col-span-12 animate__animated animate__fadeIn">
            <flux:heading size="xl" class="mb-4">Inventários Abertos</flux:heading>
            <div id="chartdiv2"></div>
        </flux:card>
        <flux:card class="col-span-6 max-lg:col-span-12 animate__animated animate__fadeIn">
            <flux:heading size="xl" class="mb-4">Horas por Produtos Fechados</flux:heading>
            <div id="chartdiv3"></div>
        </flux:card>
        <flux:card class="col-span-6 max-lg:col-span-12 animate__animated animate__fadeIn">
            <flux:heading size="xl" class="mb-4">Horas por Produtos Abertos</flux:heading>
            <div id="chartdiv4"></div>
        </flux:card>
    </div>
</div>

@assets
<style>
    #chartdiv, #chartdiv2, #chartdiv3, #chartdiv4 {
        width: 100%;
        height: 300px;
    }
</style>

<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
@endassets

<!-- Chart code -->
<script>
    am5.ready(function () {
        const dadosFechados = @json($this->fechados());
        const dataFechados = dadosFechados.map(item => ({
            dias: item.dias,
            qt: parseInt(item.qt)
        }));

        const dadosAbertos = @json($this->abertos());
        const dataAbertos = dadosAbertos.map(item => ({
            dias: item.dias,
            qt: parseInt(item.qt)
        }));

        const analiseFechados = @json($this->analiseFechados());
        const dataAnaliseFechados = analiseFechados.map(item => ({
            dias: item.codfilial,
            qt: item.horas
        }));

        const analiseAbertos = @json($this->analiseAbertos());
        const dataAnaliseAbertos = analiseAbertos.map(item => ({
            dias: item.codfilial,
            qt: item.horas
        }));

        // Primeiro gráfico
        var root1 = am5.Root.new("chartdiv");
        root1.setThemes([am5themes_Animated.new(root1)]);
        root1._logo.dispose();

        var chart1 = root1.container.children.push(am5xy.XYChart.new(root1, {
            panX: false,
            panY: false,
            wheelX: "panX",
            wheelY: "none",
            pinchZoomX: true,
            paddingLeft: 0,
            paddingRight: 1
        }));

        var cursor1 = chart1.set("cursor", am5xy.XYCursor.new(root1, {}));
        cursor1.lineY.set("visible", false);

        var xRenderer1 = am5xy.AxisRendererX.new(root1, {
            minGridDistance: 30,
            minorGridEnabled: true
        });

        xRenderer1.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        var xAxis1 = chart1.xAxes.push(am5xy.CategoryAxis.new(root1, {
            maxDeviation: 0.3,
            categoryField: "dias",
            renderer: xRenderer1,
            tooltip: am5.Tooltip.new(root1, {})
        }));

        var yAxis1 = chart1.yAxes.push(am5xy.ValueAxis.new(root1, {
            maxDeviation: 0.3,
            renderer: am5xy.AxisRendererY.new(root1, {
                strokeOpacity: 0.1
            })
        }));

        yAxis1.get("renderer").labels.template.set("visible", false);

        var series1 = chart1.series.push(am5xy.ColumnSeries.new(root1, {
            name: "Series 1",
            xAxis: xAxis1,
            yAxis: yAxis1,
            valueYField: "qt",
            categoryXField: "dias",
            tooltip: am5.Tooltip.new(root1, {
                labelText: "{valueY}"
            })
        }));

        series1.columns.template.setAll({cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0});

        series1.columns.template.adapters.add("fill", (fill, target) => {
            return chart1.get("colors").getIndex(series1.columns.indexOf(target));
        });

        series1.columns.template.adapters.add("stroke", (stroke, target) => {
            return chart1.get("colors").getIndex(series1.columns.indexOf(target));
        });

        series1.bullets.push(function () {
            return am5.Bullet.new(root1, {
                locationY: 0.5, // Centraliza verticalmente
                sprite: am5.Label.new(root1, {
                    text: "{valueY}",
                    fill: am5.color(0xffffff), // Use branco para temas escuros; pode trocar por `alternativeText`
                    centerX: am5.p50, // Centraliza horizontalmente
                    centerY: am5.p50,
                    dy: -10, // empurra para cima
                    populateText: true
                })
            });
        });

        xAxis1.data.setAll(dataFechados);
        series1.data.setAll(dataFechados);

        series1.appear(1000);
        chart1.appear(1000, 100);

        // Segundo gráfico
        var root2 = am5.Root.new("chartdiv2");
        root2.setThemes([
            am5themes_Animated.new(root2)
        ]);
        root2._logo.dispose();

        var chart2 = root2.container.children.push(am5xy.XYChart.new(root2, {
            panX: false,
            panY: false,
            wheelX: "panX",
            wheelY: "none",
            pinchZoomX: true,
            paddingLeft: 0,
            paddingRight: 1
        }));

        var cursor2 = chart2.set("cursor", am5xy.XYCursor.new(root2, {}));
        cursor2.lineY.set("visible", false);

        var xRenderer2 = am5xy.AxisRendererX.new(root2, {
            minGridDistance: 30,
            minorGridEnabled: true
        });

        xRenderer2.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        var xAxis2 = chart2.xAxes.push(am5xy.CategoryAxis.new(root2, {
            maxDeviation: 0.3,
            categoryField: "dias",
            renderer: xRenderer2,
            tooltip: am5.Tooltip.new(root2, {})
        }));

        var yAxis2 = chart2.yAxes.push(am5xy.ValueAxis.new(root2, {
            maxDeviation: 0.3,
            renderer: am5xy.AxisRendererY.new(root2, {
                strokeOpacity: 0.1
            })
        }));

        yAxis2.get("renderer").labels.template.set("visible", false);

        var series2 = chart2.series.push(am5xy.ColumnSeries.new(root2, {
            name: "Series 2",
            xAxis: xAxis2,
            yAxis: yAxis2,
            valueYField: "qt",
            categoryXField: "dias",
            tooltip: am5.Tooltip.new(root2, {
                labelText: "{valueY}"
            })
        }));

        series2.columns.template.setAll({cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0});

        series2.columns.template.adapters.add("fill", (fill, target) => {
            return chart2.get("colors").getIndex(series2.columns.indexOf(target));
        });

        series2.columns.template.adapters.add("stroke", (stroke, target) => {
            return chart2.get("colors").getIndex(series2.columns.indexOf(target));
        });

        series2.bullets.push(function () {
            return am5.Bullet.new(root2, {
                locationY: 0.5, // Centraliza verticalmente
                sprite: am5.Label.new(root2, {
                    text: "{valueY}",
                    fill: am5.color(0xffffff), // Use branco para temas escuros; pode trocar por `alternativeText`
                    centerX: am5.p50, // Centraliza horizontalmente
                    centerY: am5.p50,
                    dy: -10,
                    populateText: true
                })
            });
        });


        xAxis2.data.setAll(dataAbertos);
        series2.data.setAll(dataAbertos);

        series2.appear(1000);
        chart2.appear(1000, 100);

        // Terceiro gráfico
        var root3 = am5.Root.new("chartdiv3");
        root3.setThemes([
            am5themes_Animated.new(root3)
        ]);
        root3._logo.dispose();

        var chart3 = root3.container.children.push(am5xy.XYChart.new(root3, {
            panX: false,
            panY: false,
            wheelX: "panX",
            wheelY: "none",
            pinchZoomX: true,
            paddingLeft: 0,
            paddingRight: 1
        }));

        var cursor3 = chart3.set("cursor", am5xy.XYCursor.new(root3, {}));
        cursor3.lineY.set("visible", false);

        var xRenderer3 = am5xy.AxisRendererX.new(root3, {
            minGridDistance: 30,
            minorGridEnabled: true
        });

        xRenderer3.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        var xAxis3 = chart3.xAxes.push(am5xy.CategoryAxis.new(root3, {
            maxDeviation: 0.3,
            categoryField: "dias",
            renderer: xRenderer3,
            tooltip: am5.Tooltip.new(root3, {})
        }));

        var yAxis3 = chart3.yAxes.push(am5xy.ValueAxis.new(root3, {
            maxDeviation: 0.3,
            renderer: am5xy.AxisRendererY.new(root3, {
                strokeOpacity: 0.1
            })
        }));

        var series3 = chart3.series.push(am5xy.ColumnSeries.new(root3, {
            name: "Series 3",
            xAxis: xAxis3,
            yAxis: yAxis2,
            valueYField: "qt",
            categoryXField: "dias",
            tooltip: am5.Tooltip.new(root3, {
                labelText: "{valueY}"
            })
        }));

        series3.columns.template.setAll({cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0});

        series3.columns.template.adapters.add("fill", (fill, target) => {
            return chart3.get("colors").getIndex(series3.columns.indexOf(target));
        });

        series3.columns.template.adapters.add("stroke", (stroke, target) => {
            return chart3.get("colors").getIndex(series3.columns.indexOf(target));
        });

        series3.columns.template.set("tooltipText", "{categoryX}: {valueY}");

        series3.bullets.push(function () {
            return am5.Bullet.new(root3, {
                locationY: 0.5, // Centraliza verticalmente
                sprite: am5.Label.new(root3, {
                    text: "{valueY}",
                    fill: am5.color(0xffffff), // Use branco para temas escuros; pode trocar por `alternativeText`
                    centerX: am5.p50, // Centraliza horizontalmente
                    centerY: am5.p50,
                    dy: -10,
                    populateText: true
                })
            });
        });

        xAxis3.data.setAll(dataAnaliseFechados);
        series3.data.setAll(dataAnaliseFechados);

        series3.appear(1000);
        chart3.appear(1000, 100);

        // Quarto gráfico
        var root4 = am5.Root.new("chartdiv4");
        root4.setThemes([am5themes_Animated.new(root4)]);
        root4._logo.dispose();

        var chart4 = root4.container.children.push(am5xy.XYChart.new(root4, {
            panX: false,
            panY: false,
            wheelX: "panX",
            wheelY: "none",
            pinchZoomX: true,
            paddingLeft: 0,
            paddingRight: 1
        }));

        var cursor4 = chart4.set("cursor", am5xy.XYCursor.new(root4, {}));
        cursor4.lineY.set("visible", false);

        var xRenderer4 = am5xy.AxisRendererX.new(root4, {
            minGridDistance: 30,
            minorGridEnabled: true
        });

        xRenderer4.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        var xAxis4 = chart4.xAxes.push(am5xy.CategoryAxis.new(root4, {
            maxDeviation: 0.3,
            categoryField: "dias",
            renderer: xRenderer4,
            tooltip: am5.Tooltip.new(root4, {})
        }));

        var yAxis4 = chart4.yAxes.push(am5xy.ValueAxis.new(root4, {
            maxDeviation: 0.3,
            renderer: am5xy.AxisRendererY.new(root4, {
                strokeOpacity: 0.1
            })
        }));

        var series4 = chart4.series.push(am5xy.ColumnSeries.new(root4, {
            name: "Series 4",
            xAxis: xAxis4,
            yAxis: yAxis2,
            valueYField: "qt",
            categoryXField: "dias",
            tooltip: am5.Tooltip.new(root4, {
                labelText: "{valueY}"
            })
        }));

        series4.columns.template.setAll({cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0});

        series4.columns.template.adapters.add("fill", (fill, target) => {
            return chart4.get("colors").getIndex(series4.columns.indexOf(target));
        });

        series4.columns.template.adapters.add("stroke", (stroke, target) => {
            return chart4.get("colors").getIndex(series4.columns.indexOf(target));
        });

        series4.columns.template.set("tooltipText", "{categoryX}: {valueY}");

        series4.bullets.push(function () {
            return am5.Bullet.new(root4, {
                locationY: 0.5, // Centraliza verticalmente
                sprite: am5.Label.new(root4, {
                    text: "{valueY}",
                    fill: am5.color(0xffffff), // Use branco para temas escuros; pode trocar por `alternativeText`
                    centerX: am5.p50, // Centraliza horizontalmente
                    centerY: am5.p50,
                    dy: -10,
                    populateText: true
                })
            });
        });

        xAxis4.data.setAll(dataAnaliseAbertos);
        series4.data.setAll(dataAnaliseAbertos);

        series4.appear(1000);
        chart4.appear(1000, 100);

        // cor
        const updateChartColors = (isDark) => {
            const labelColor = isDark ? am5.color(0xFFFFFF) : am5.color(0x000000);

            xRenderer1.labels.template.setAll({fill: labelColor});
            yAxis1.get("renderer").labels.template.setAll({fill: labelColor});
            series1.get("tooltip").label.setAll({fill: labelColor});

            xRenderer2.labels.template.setAll({fill: labelColor});
            yAxis2.get("renderer").labels.template.setAll({fill: labelColor});
            series2.get("tooltip").label.setAll({fill: labelColor});

            xRenderer3.labels.template.setAll({fill: labelColor});
            yAxis3.get("renderer").labels.template.setAll({fill: labelColor});
            series3.get("tooltip").label.setAll({fill: labelColor});

            xRenderer4.labels.template.setAll({fill: labelColor});
            yAxis4.get("renderer").labels.template.setAll({fill: labelColor});
            series4.get("tooltip").label.setAll({fill: labelColor});
        };

        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.classList.contains('dark');
            updateChartColors(isDark);
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
</script>
