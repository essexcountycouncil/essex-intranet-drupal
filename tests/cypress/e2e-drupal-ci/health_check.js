describe('Visit health-check', () => {

  it('Sees "Passed"', () => {

    cy.request({
      url: '/health-check'
    }).then((response) => {
      expect(response.status).to.eq(200)
      expect(response.body).to.contain('Passed')
    })
  })
})
