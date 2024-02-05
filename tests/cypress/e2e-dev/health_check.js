describe('Visit health-check', () => {

  it('Sees "Passed"', () => {

    cy.request({
      url: '/08b5d5dc-f2de-4c78-86a7-d3ea80037430'
    }).then((response) => {
      expect(response.status).to.eq(200)
      expect(response.body).to.contain('Passed')
    })
  })
})
