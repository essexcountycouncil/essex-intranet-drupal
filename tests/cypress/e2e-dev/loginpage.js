describe('Visit user login page', () => {

  it('Sees login form', () => {

    cy.visit('/user')
    cy.contains('Log in').should('be.visible')
    cy.contains('Log in with Nomensa').should('be.visible')
  })
})
