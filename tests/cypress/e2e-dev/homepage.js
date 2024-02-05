describe('Visit homepage', () => {

  it('Page loads', () => {

    cy.visit('/')
    cy.contains('Essex').should('be.visible')
  })
})
