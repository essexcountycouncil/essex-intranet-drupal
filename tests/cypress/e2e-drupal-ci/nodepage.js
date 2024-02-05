describe('Visit a node page', () => {

  it('Sees content', () => {

    cy.visit('/node/1')
    cy.contains('LocalGov Drupal').should('be.visible')
  })
})
