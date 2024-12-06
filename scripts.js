// JavaScript to switch between tabs and show appropriate content
function switchTab(tabName) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-button');

    // Hide all tabs
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    // Remove active class from all buttons
    buttons.forEach(button => {
        button.classList.remove('active');
    });

    // Show the selected tab and add active class to the clicked button
    document.getElementById(tabName).classList.add('active');
    const activeButton = document.querySelector(`button[onclick="switchTab('${tabName}')"]`);
    activeButton.classList.add('active');
}
