<template>
	<view class="content">
		 <div class="contain-map">
                <map 
				  class="map"
				  :scale='5'
				 :latitude="latitude" 
				 :longitude="longitude" 
				 :markers='markers'
				 :circles="circles"
				 >
                </map>
		 </div>     
	</view>
</template>

<script>
	export default {
		data() {
				return {
						latitude:30.547441,
						longitude: 104.061738,
						markers:[
							{latitude:30.547441,longitude:104.061738},
							{latitude:29.557300,longitude:106.577150},
							{latitude:34.259430,longitude:108.947040},
						],
					   //  polylines:[{
								// points: [
								// 	{latitude:30.547441,longitude:104.061738},
								// 	{latitude:29.557300,longitude:106.577150},
								// 	{latitude:34.259430,longitude:108.947040},
								// 	{latitude:30.547441,longitude:104.061738},
								// ],
								// arrowLine: true,
								// dottedLine: true,
								// arrowIconPath: "/static/images/2btn_loc/btn_loc3.png",
								// width: 2,
								// color: "FEAA34"
					   //   }]
						　　circles:[{  //在地图上显示圆
						   　　latitude: 30.547441,
						   　　longitude: 104.061738,
						   　　fillColor:"#999999",//填充颜色
						  　　 color:"#f00",//描边的颜色
						   　　radius:20,//半径
						  　　 strokeWidth:2//描边的宽度
						   }],
			
					}
		},
		onLoad() {
             this.getmarkers();
		},
		methods: {
           getmarkers(){
			   uni.request({
			       url: this.$url+'getMarkers',
			       success: (res) => {
                         console.log(res.data)
			       }
			   });
		   }

		}
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}
	.contain-map{
		position: fixed;
		top:44px;
		bottom:0;
		left: 0;
		right:0;
	}
	.map{
		width: 100%;
		height: 100%;
	}
</style>
