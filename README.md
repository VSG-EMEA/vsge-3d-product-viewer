# VSGE plugin for internal use, adds a 3d model to woocommerce gallery using model viewer

In order to enable the 3d model you need some custom metadata usually stored with the "vsge importer"

here a recap of the 3d model metadata
{
	"media": {
		"3d_model": [
			"3dmodel.glb"
		],
		"3d_model_preview": [
			"preview.png"
		],
		"3d_model_data": {
			"camera-orbit": "65deg 90deg 25m",
			"camera-target": "0m 1m 0m",
			"hotspots": [
				{
					"slot": "pin3",
					"position": "0 0.75 0.75",
					"title": "Pin 1"
				},
				{
					"slot": "pin3",
					"position": "0.01 1.25 -1",
					"title": "Pin 2",
					"href": "https://url"
				},
				{
					"slot": "pin3",
					"position": "0.62 0.05 0.58",
					"title": "Pin 3",
          "href": "https://url"
				}
			]
		}
	}
}
