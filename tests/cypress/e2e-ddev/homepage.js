describe('Visit homepage', () => {

  it('Page loads', () => {

    cy.visit('/')
    cy.contains('Gavin').should('be.visible')
  })
})
