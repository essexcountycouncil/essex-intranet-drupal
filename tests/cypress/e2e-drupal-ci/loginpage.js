describe('Visit user login page', () => {

  it('Sees login form', () => {

    cy.visit('/user')
    cy.contains('Log in').should('be.visible')
    cy.contains('Enter your Knowsley Borough Council username').should('be.visible')
  })
})
