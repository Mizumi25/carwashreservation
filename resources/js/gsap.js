import { Draggable } from 'gsap/Draggable';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { CSSRulePlugin } from 'gsap/CSSRulePlugin';

gsap.registerPlugin(ScrollTrigger, CSSRulePlugin);
  





gsap.registerPlugin(Draggable);




import Lenis from 'lenis';

import { GLTFLoader } from 'three-stdlib'; // GLTFLoader for 3D model loading
import { EffectComposer } from 'three-stdlib'; // Post-processing EffectComposer
import { RenderPass } from 'three-stdlib'; // Render pass for EffectComposer
import { UnrealBloomPass } from 'three-stdlib'; // Unreal Bloom for glowing effects
import { OrbitControls } from 'three-stdlib';

import * as THREE from 'three';


const scene = new THREE.Scene();
scene.background = new THREE.Color(0x151620);

const camera = new THREE.PerspectiveCamera(
  70,
  window.innerWidth / window.innerHeight,
  0.1,
  1000
);
camera.position.set(60, 10, 50);
camera.lookAt(0, 0, 0);

const renderer = new THREE.WebGLRenderer({
  antialias: true,
  powerPreference: "high-performance",
  alpha: true,
});
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 0.75;
document.querySelector(".model").appendChild(renderer.domElement);

const ambientLight = new THREE.AmbientLight(0xffffff, 0);
scene.add(ambientLight);

const directionalLight = new THREE.DirectionalLight(0xcc8ee8, 1.5);
directionalLight.position.set(5, 5, 5);
directionalLight.castShadow = true;
directionalLight.shadow.mapSize.width = 2048;
directionalLight.shadow.mapSize.height = 2048;
scene.add(directionalLight);

const pointLight = new THREE.PointLight(0xffd600, 3, 50);
pointLight.position.set(4.5, 25, 25);
pointLight.decay = 5;
scene.add(pointLight);

const pointLight2 = new THREE.PointLight(0xea00ff, 1.25, 0);
pointLight2.position.set(-100, 65.5, 20);
pointLight2.decay = 2;
scene.add(pointLight2);

const pointLight3 = new THREE.PointLight(0xff4c00, 2.5, 50);
pointLight3.position.set(10, -10, -25);
pointLight3.decay = 2;
scene.add(pointLight3);

const pointLight4 = new THREE.PointLight(0xffd600, 3, 47);
pointLight4.position.set(52, -25, 25);
pointLight4.decay = 0.5;
scene.add(pointLight4);

const composer = new EffectComposer(renderer);
const renderPass = new RenderPass(scene, camera);
composer.addPass(renderPass);

const bloomPass = new UnrealBloomPass(
  new THREE.Vector2(window.innerWidth, window.innerHeight),
  0.6,
  1,
  0.1
);
composer.addPass(bloomPass);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.dampingFactor = 0.05;
controls.minDistance = 10;
controls.maxDistance = 50;
controls.maxPolarAngle = Math.PI / 2;

const createEmissiveMaterial = (color, intensity = 2) => {
  return new THREE.MeshStandardMaterial({
    color: color,
    emissive: color,
    emissiveIntensity: intensity,
    toneMapped: false,

  });
};

scene.add(createEmissiveMaterial);

const loader = new GLTFLoader();
loader.load("./3dmodels/scene.gltf", function (gltf) {
  const model = gltf.scene;

  const box = new THREE.Box3().setFromObject(model);
  const center = box.getCenter(new THREE.Vector3());
  model.position.sub(center).add(new THREE.Vector3(20, 0, 0));

  scene.add(model);
});

function animate() {
  requestAnimationFrame(animate);
  controls.update();
  composer.render();
}

animate();






gsap.to("#navigationWelcome", {
  backgroundColor: "#ffffff", 
  scrollTrigger: {
    trigger: "#Welcome2",       
    start: "bottom top",    
    toggleActions: "play none none reverse", 
    markers: false          
  }
});
gsap.to("#navigationWelcome", {
  backgroundColor: "#66ff66", 
  scrollTrigger: {
    trigger: "#footer",        
    start: "top bottom",      
    toggleActions: "play none none reverse",
    markers: false
  }
});
gsap.to("#navigationWelcome a", {
  color: "#000000", 
  scrollTrigger: {
    trigger: "#Welcome2",       
    start: "bottom top",    
    toggleActions: "play none none reverse", 
    markers: false          
  }
});
gsap.to(".navigationWelcome h1", {
  color: "#000000", 
  scrollTrigger: {
    trigger: "#Welcome2",       
    start: "bottom top",    
    toggleActions: "play none none reverse", 
    markers: false          
  }
});




const scrollTriggerSettings = {
  trigger: "#Welcome2",
  start: "top 25%",
  toggleActions: "play reverse play reverse"
};

const leftXValues = [-800, -900, -400];
const rightXValues = [800, 900, 400];
const leftRotationValues = [-30, -20, -35];
const rightRotationValues = [30, 20, 35];
const yValues = [100, -150, -400];

gsap.utils.toArray(".row").forEach((row, index) => {
  const cardLeft = row.querySelector(".card-left");
  const cardRight = row.querySelector(".card-right");

  // Animate the left card
  gsap.to(cardLeft, {
    x: leftXValues[index],
    y: yValues[index],
    rotation: leftRotationValues[index],
    scrollTrigger: {
      trigger: "#Welcome2",
      start: "top center",
      end: "150% bottom",
      scrub: true,
      onUpdate: (self) => {
        const progress = self.progress;
        cardLeft.style.transform = `translateX(${progress * leftXValues[index]}px) translateY(${progress * yValues[index]}px) rotate(${progress * leftRotationValues[index]}deg)`;
      },
    }
  });

  // Animate the right card
  gsap.to(cardRight, {
    x: rightXValues[index],
    y: yValues[index],
    rotation: rightRotationValues[index],
    scrollTrigger: {
      trigger: "#Welcome2",
      start: "top center",
      end: "150% bottom",
      scrub: true,
      onUpdate: (self) => {
        const progress = self.progress;
        cardRight.style.transform = `translateX(${progress * rightXValues[index]}px) translateY(${progress * yValues[index]}px) rotate(${progress * rightRotationValues[index]}deg)`;
      },
    }
  });
});

// Animate line paragraphs
gsap.to(".line p", {
  y: 0,
  stagger: 0.1,
  duration: 0.5,
  ease: "power1.out",
  scrollTrigger: scrollTriggerSettings,
});





