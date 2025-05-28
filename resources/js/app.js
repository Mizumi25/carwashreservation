import './bootstrap';

import ApexCharts from 'apexcharts';






import '../../vendor/masmerise/livewire-toaster/resources/js'; 

import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

import '@fortawesome/fontawesome-free/css/all.css';


    function playClickSound() {
        var audio = new Audio('./sounds/sound.mp3'); 
        audio.play();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var buttons = document.querySelectorAll('button');

        buttons.forEach(function(button) {
            button.addEventListener('click', playClickSound);
        });
    });






  function generateRows() {
    const rows = [];

    for (let i = 1; i <= 3; i++) {
      // Create the row container
      const row = document.createElement("div");
      row.classList.add("row");

      // Left card
      const cardLeft = document.createElement("div");
      cardLeft.classList.add("card", "card-left");
      const imgLeft = document.createElement("img");
      imgLeft.src = `images//img-${2 * i - 1}.jpeg`;
      imgLeft.alt = "";
      cardLeft.appendChild(imgLeft);

      // Right card
      const cardRight = document.createElement("div");
      cardRight.classList.add("card", "card-right");
      const imgRight = document.createElement("img");
      imgRight.src = `images//img-${2 * i}.jpeg`;
      imgRight.alt = "";
      cardRight.appendChild(imgRight);

      
      row.appendChild(cardLeft);
      row.appendChild(cardRight);

      
      rows.push(row);
    }

    return rows;
  }

  
  function renderRows() {
    const section = document.getElementById("Welcome2");
    const rows = generateRows();
    
    rows.forEach(row => {
      section.appendChild(row);
    });
  }

  // Call renderRows to display the images
  renderRows();





   

