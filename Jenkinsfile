pipeline {

    agent any

    environment {
        IMAGE_NAME = "dockerforlearning0213/todo-app"
        IMAGE_TAG  = "${BUILD_NUMBER}"
        NAMESPACE  = "todo-app"
        DEPLOYMENT = "todo-app"
        CONTAINER  = "todo-app"
    }

    stages {

        stage('Checkout Source') {
            steps {
                echo "========== Checking Out Source =========="
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                sh '''
                    echo "========== Building Docker Image =========="

                    docker build -t ${IMAGE_NAME}:${IMAGE_TAG} .

                    echo ""
                    echo "========== Docker Images =========="
                    docker images | grep todo-app
                '''
            }
        }

        stage('Push Docker Image') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub',
                        usernameVariable: 'DOCKER_USER',
                        passwordVariable: 'DOCKER_PASS'
                    )
                ]) {

                    sh '''
                        echo "========== Docker Login =========="

                        echo "$DOCKER_PASS" | docker login \
                        -u "$DOCKER_USER" \
                        --password-stdin

                        echo ""
                        echo "========== Pushing Image =========="

                        docker push ${IMAGE_NAME}:${IMAGE_TAG}

                        docker logout
                    '''
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                withCredentials([
                    file(credentialsId: 'kubeconfig', variable: 'KUBECONFIG')
                ]) {

                    script {

                        try {

                            echo "========== Deploying Image =========="

                            sh """
                                kubectl set image deployment/${DEPLOYMENT} \
                                ${CONTAINER}=${IMAGE_NAME}:${IMAGE_TAG} \
                                -n ${NAMESPACE}
                            """

                            echo "========== Waiting for Rollout =========="

                            sh """
                                kubectl rollout status deployment/${DEPLOYMENT} \
                                -n ${NAMESPACE} \
                                --timeout=120s
                            """

                            echo "========== Deployment Successful =========="

                        } catch (Exception e) {

                            echo "========== Deployment Failed =========="
                            echo "${e}"

                            echo "========== Rolling Back =========="

                            sh """
                                kubectl rollout undo deployment/${DEPLOYMENT} \
                                -n ${NAMESPACE} || true
                            """

                            sh """
                                kubectl rollout status deployment/${DEPLOYMENT} \
                                -n ${NAMESPACE} \
                                --timeout=120s || true
                            """

                            error("Rollback completed. Deployment failed.")
                        }
                    }
                }
            }
        }
    }

    post {

        success {
            echo "========================================="
            echo "Deployment Successful"
            echo "Image      : ${IMAGE_NAME}:${IMAGE_TAG}"
            echo "Namespace  : ${NAMESPACE}"
            echo "========================================="
        }

        failure {
            echo "========================================="
            echo "Deployment Failed"
            echo "Rollback Executed (if previous revision existed)"
            echo "========================================="
        }

        always {
            echo "========== Pipeline Finished =========="
        }
    }
}
