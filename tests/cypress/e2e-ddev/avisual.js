describe('Footer', () => {
  it('should display the footer correctly', () => {
    cy.visit('/');
    cy.contains('LocalGov Drupal').should('be.visible')
    cy.get('.lgd-footer').compareSnapshot('just-footer', 0.1)

  })
})