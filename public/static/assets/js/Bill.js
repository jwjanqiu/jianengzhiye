var arr1=[],arr2=[],arr3=[],arr4=[];
function getMonthData() {
    $.ajax({
        type:"post",
        async:false,
        url:"monthBill",
        data:{},
        dataType:"json",
        success:function (result) {
            if (result){
                for(var i = 0; i < result.length; i++){
                    // arr1.push(result[i].goods);
                    arr2.push(result[i]);
                }
            }
        }
    })
    return arr2;
}
function getSeasonData() {
    $.ajax({
        type:"post",
        async:false,
        url:"seasonBill",
        data:{},
        dataType:"json",
        success:function (result) {
            if (result){
                for(var i = 0; i < result.length; i++){
                    arr1.push(result[i]);
                }
            }
        }
    })
    return arr1;
}
function getFiveDaysBill() {
    $.ajax({
        type:"post",
        async:false,
        url:"fiveDaysBill",
        data:{},
        dataType:"json",
        success:function (result) {
            if (result){
                for(var i = 0; i < result.length; i++){
                    arr3.push(result[i].time);
                    arr4.push(result[i].sum);
                }
            }
        }
    })
    return arr3,arr4;
}
getMonthData();
getSeasonData();
getFiveDaysBill();
console.log(arr1);
console.log(arr2);
console.log(arr3);
console.log(arr4);
var echartsD = echarts.init(document.getElementById('tpl-echarts-D'));
optionD = {
    tooltip:{
        trigger: 'axis'
    },
    legend:{
        data:['total_price']
    },
    xAxis:[
        {
            type:'category',
            boundaryGap: true,
            data:['一月', '二月', '三月', '四月', '五月', '六月', '七月','八月','九月','十月','十一月','十二月']
        }
    ],
    yAxis:[
        {
            type:'value'
        }
    ],
    series:[
        {
            name:"销售额",
            type:"bar",
            data:arr2
        }
    ]
};
echartsD.setOption(optionD)
var echartsA = echarts.init(document.getElementById('tpl-echarts-A'));
optionA = {
    tooltip:{
        trigger: 'axis'
    },
    legend:{
        data:['total_price']
    },
    xAxis:[
        {
            type:'category',
            boundaryGap: true,
            data:['第一季度', '第二季度', '第三季度', '第四季度']
        }
    ],
    yAxis:[
        {
            type:'value'
        }
    ],
    series:[
        {
            name:"销售额",
            type:"bar",
            data:arr1,
            itemStyle: {
                normal: {
                    color: '#1cabdb',
                    borderColor: '#1cabdb',
                    borderWidth: '2',
                    borderType: 'solid',
                    opacity: '1'
                },
                emphasis: {

                }
            }
        }
    ]
};
echartsA.setOption(optionA)

/*
var echartsF = echarts.init(document.getElementById('tpl-echarts-F'));
option = {
    tooltip: {
        trigger: 'axis'
    },
    grid: {
        top: '3%',
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: [{
        type: 'category',
        boundaryGap: false,
        data: arr3
    }],
    yAxis: [{
        type: 'value'
    }],
    textStyle: {
        color: '#838FA1'
    },
    series: [{
        name: '营销额',
        type: 'line',
        stack: '总量',
        areaStyle: { normal: {} },
        data: arr4,
        itemStyle: {
            normal: {
                color: '#1cabdb',
                borderColor: '#1cabdb',
                borderWidth: '2',
                borderType: 'solid',
                opacity: '1'
            },
            emphasis: {

            }
        }
    }]
};

echartsF.setOption(option);
*/
var echartsA = echarts.init(document.getElementById('tpl-echarts'));
option = {
    tooltip: {
        trigger: 'axis'
    },
    grid: {
        top: '3%',
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: [{
        type: 'category',
        boundaryGap: false,
        data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
    }],
    yAxis: [{
        type: 'value'
    }],
    textStyle: {
        color: '#838FA1'
    },
    series: [{
        name: '邮件营销',
        type: 'line',
        stack: '总量',
        areaStyle: { normal: {} },
        data: [120, 132, 101, 134, 90],
        itemStyle: {
            normal: {
                color: '#1cabdb',
                borderColor: '#1cabdb',
                borderWidth: '2',
                borderType: 'solid',
                opacity: '1'
            },
            emphasis: {

            }
        }
    }]
};

echartsA.setOption(option);
