<!DOCTYPE html>
<html lang="en" style="padding:0px;margin:0px;overflow:hidden;border:0px;">
<head>
    <title>SceneJS Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <style>
        body {
            background: white;
            margin: 0;
			padding : 0;
            -moz-user-select: -moz-none;
            -khtml-user-select: none;
            -webkit-user-select: none;
        }
    </style>
</head>
<body style="padding:0px;margin:0px;border:0px;overflow:hidden;">

<!--<script src="http://nicer.app/nicerapp/lib/scenejs-3.0/api/latest/scenejs.js"></script>-->
<script src="http://scenejs.org/api/latest/scenejs.js"></script>

<!--<script src="http://nicer.app/nicerapp/lib/scenejs-3.0/api/latest/extras/orbitControl.js"></script>-->
<script src="http://scenejs.org/api/latest/extras/orbitControl.js"></script>

<script>

var scene;
var bubbles;
var bubbleNames;

createScene();
createBubbles();

function createScene() {
    scene = SceneJS.createScene({
        nodes:[
            {
                type:"lookAt",
                eye:{ y:40 },
                nodes:[
                    {
                        type:"lights",
                        lights:[
                            {
                                mode:"ambient",
                                color:{ r:0.7, g:0.7, b:0.9 },
                                diffuse:true,
                                specular:false
                            },
                            {
                                mode:"dir",
                                color:{ r:1.0, g:1.0, b:0.2 },
                                diffuse:true,
                                specular:true,
                                dir:{ x:1.0, y:0.9, z:-0.7 },
                                space:"world"
                            }
                        ],

                        nodes:[

                            //--------------------------------------------------------------------------------
                            // Wire grid floor
                            //--------------------------------------------------------------------------------

                            {
                                id:"grid-floor",
                                type:"material",
                                color:{ r:0.3, g:0.3, b:0.3 },
                                emit:1,
                                nodes:[
                                    {
                                        type:"scale",
                                        x:2000,
                                        y:.5,
                                        z:2000,
                                        nodes:[
                                            {
                                                type:"rotate",
                                                x:1,
                                                angle:90,
                                                nodes:[

                                                    // Wireframe grid plane
                                                    {
                                                        type:"geometry",
                                                        source:{
                                                            type:"plane",
                                                            wire:true,
                                                            widthSegments:200,
                                                            heightSegments:200
                                                        }
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                ]
                            },

                            //--------------------------------------------------------------------------------
                            // Bubbles
                            //--------------------------------------------------------------------------------

                            // Shader node, injects custom functions into the SceneJS fragment shader
                            // for a cool X-Ray transparency effect
                            {
                                type:"shader",
                                id:"myShader",

                                "shaders":[

                                    // We'll just customize the fragment shader
                                    {
                                        "stage":"fragment",
                                        "code":[

                                            // Input for this shader - the degree of opacity from [0..1]
                                            "uniform float  xrayOpacity;",

                                            // User-injected function to intercept the World-space fragment normal
                                            "vec3 myworldNormal = vec3(0.0, 0.0,  1.0);",
                                            "void myWorldNormalFunc(vec3 vec) {",
                                            "   myworldNormal = vec;",
                                            "}",

                                            // User-injected function to intercept the World-space fragment-eye vector
                                            "vec3 myworldEyeVec = vec3(0.0, 0.0, -1.0);",
                                            "void myWorldEyeVecFunc(vec3 vec) {",
                                            "   myworldEyeVec = vec;",
                                            "}",

                                            // User-injected function to alter the alpha of the outgoing fragment color,
                                            // in proportion to the angle between the fragment normal and the
                                            // fragment-eye vector
                                            "vec4 myPixelColorFunc(vec4 color) {",
                                            "   color.a = (xrayOpacity + 0.9 - abs(dot(myworldNormal, myworldEyeVec)));",
                                            "   return color;",
                                            "}"
                                        ],

                                        // Bind our injected functions to SceneJS hook points
                                        hooks:{
                                            worldNormal:"myWorldNormalFunc",
                                            worldEyeVec:"myWorldEyeVecFunc",
                                            pixelColor:"myPixelColorFunc"
                                        }
                                    }
                                ],

                                // Provide optional initial value for our input uniform
                                params:{
                                    xrayOpacity:0.3
                                },

                                nodes:[

                                    // Disable backfaces (they look ugly with transparency)
                                    // Enable transparency (causing geometries to go into
                                    // the renderer's transparency bin)
                                    {
                                        type:"flags",
                                        flags:{
                                            transparent:true,
                                            backfaces:false
                                        },
                                        nodes:[

                                            // Oily texture
                                            {
                                                type:"texture",
                                                layers:[
                                                    {
                                                        src:"http://nicer.app/nicerapp/lib/scenejs-3.0/examples/textures/oilySheen.jpg",
                                                        applyTo:"color"
                                                    }
                                                ],
                                                nodes:[

                                                    // Bluish color
                                                    {
                                                        type:"material",
                                                        coreId:"bubble",
                                                        emit:0.1,
                                                        color:{ r:0.9, g:0.8, b:1.0 },
                                                        specularColor:{ r:1.0, g:1.0, b:1.0 },
                                                        specular:1.0,
                                                        shine:10.0,
                                                        nodes:[

                                                            // The bubbles go here
                                                            {
                                                                type:"node",
                                                                id:"content"
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        ]

                                    }
                                ]
                            }
                        ]
                    },

                    //--------------------------------------------------------------------------------
                    // Sky box
                    //--------------------------------------------------------------------------------

                    // Shader node, injects custom function into the vertex shader
                    // to intercept the View matrix and remove translation terms from it.
                    // This anchors the sky box, preventing it from translating
                    // as we move the eye position.
                    {
                        type:"shader",
                        shaders:[
                            {
                                stage:"vertex",
                                code:[
                                    "mat4 myViewMatrix(mat4 m) {",
                                    "   m[3][0] =m[3][1] = m[3][2] = 0.0;",
                                    "return m;",
                                    "}"
                                ],
                                // Bind our injected functions to SceneJS hook points
                                hooks:{
                                    viewMatrix:"myViewMatrix"
                                }
                            }
                        ],
                        nodes:[

                            // Disable lighting for the sky box
                            {
                                type:"flags",
                                flags:{
                                    specular:false,
                                    diffuse:false,
                                    ambient:false
                                },
                                nodes:[

                                    {
                                        type:"material",
                                        color:{ r:0, g:0, b:0  },
                                        emit:0.0,
                                        nodes:[
                                            // Clouds texture
                                            {
                                                type:"texture",
                                                layers:[
                                                    {
                                                        src:"http://nicer.app/nicerapp/lib/scenejs-3.0/examples/textures/clouds-box.jpg",
                                                        blendMode:"add"
                                                    }
                                                ],
                                                nodes:[
                                                    {
                                                        type:"scale",
                                                        x:1000,
                                                        y:1000,
                                                        z:1000,
                                                        nodes:[
                                                            // Sky box geometry
                                                            {
                                                                type:"geometry",
                                                                source:{
                                                                    type:"skybox"
                                                                }
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    });

}

// Bubble factory

function createBubbles() {
    var num = 100;
    var contentNode = scene.getNode("content");
    bubbles = [];
    bubbleNames = {};
    var bubble;

    for (var i = 0; i < num; i++) {

        bubble = new (function () {

            var scale = 0.2 + (Math.random() * 2);

            this.reset = function () {
                this.pos = {
                    x:(Math.random() * 20) - 10,
                    y:2,
                    z:(Math.random() * 20) - 10
                };
                this.dir = {
                    x:0,
                    y:0.1 + Math.random() * 0.1,
                    z:0
                };
                this.age = 0;
                this.wobbleAge = 0;
                this.lifeSpan = 10000 + Math.random() * 10000;
                this.popped = false;
            };

            this.reset();

            var name = "o" + i;
            this.name = contentNode.addNode({
                type:"name",
                name:name
            });
            bubbleNames[name] = this;

            this.translate = this.name.addNode({
                type:"translate"
            });
            this.size = this.translate.addNode({
                type:"scale",
                x:scale,
                y:scale,
                z:scale
            });
            this.rotate = this.size.addNode({
                type:"rotate",
                x:Math.random() - 0.5,
                y:Math.random() - 0.5,
                z:Math.random() - 0.5,
                angle:0
            });
            this.rotate.addNode({
                type:"geometry",
                //      coreId:"bubble", BROKEN
                source:{
                    type:"sphere"
                }
            });
            this.angle = 0;
            this.angleInc = Math.random() - 0.5;
        })();

        bubbles.push(bubble);
    }

    var t = (new Date()).getTime();
    scene.on("tick",
            function () {
                var timeNow = (new Date()).getTime();
                var timeSinceLast = timeNow - t;
                var bubble;
                var pos;
                for (var i = 0; i < num; i++) {

                    bubble = bubbles[i];

                    if (bubble.popped) {
                        bubble.reset();

                    } else {

                        bubble.age += timeSinceLast;

                        pos = bubble.pos;

                        if (bubble.age > bubble.lifeSpan) {
                            bubble.reset();
                        } else {
                            pos.x += bubble.dir.x;
                            pos.y += bubble.dir.y;
                            pos.z += bubble.dir.z;
                        }
                    }

                    bubble.translate.setXYZ(pos);

                    if (++bubble.wobbleAge > 20 + (Math.random() * 10)) {
                        bubble.dir.x += (Math.random() - 0.5) * 0.1;
                        bubble.dir.z += (Math.random() - 0.5) * 0.1;
                        bubble.wobbleAge = 0;
                    }

                    bubble.rotate.setAngle(bubble.angle += bubble.angleInc);
                }
                t = timeNow;
            });
}

// Bubble pick handler, sets bubbles into 'popped' state when picked

scene.on("pick",
        function (params) {
            var bubble = bubbleNames[params.name];
            bubble.popped = true;
        });

// Mouse lookat orbit helper

new SceneJS.OrbitControls(scene, {
    yaw:30,
    pitch:-30,
    zoom:100,
    zoomSensitivity:5.0,
    eye:{ y:40 },
    look:{ y:40 }
});

</script>
</body>
</html>
