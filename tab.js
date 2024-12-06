// Function to switch between tabs
function switchTab(tabName) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Show the clicked tab
    const activeTab = document.getElementById(tabName);
    activeTab.classList.add('active');

    // Change active tab button style
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => button.classList.remove('active'));
    const activeButton = document.querySelector(`.tab-button[onclick="switchTab('${tabName}')"]`);
    activeButton.classList.add('active');
}
