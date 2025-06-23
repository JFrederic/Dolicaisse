// Modal creation client

const clientModal = document.getElementById('clientModal')
const clientName = document.getElementById('clientName')
clientModal.addEventListener('shown.bs.modal', () => {
  clientName.focus()
})